# -*- coding: utf-8 -*-
"""
Drycured Podcast Engine v1
Automatski TTS proizvodni tok za ElevenLabs.

Radi:
1) učita manifest epizoda
2) učita scenarije s oznakama VODITELJ / MAJSTOR
3) generira MP3 segment po segment
4) spaja segmente u finalne MP3 epizode preko FFmpeg-a
5) izrađuje WordPress CSV za objavu podcasta

Sigurnost:
- API key se čita iz .env datoteke
- API key se ne ispisuje u log
"""

from __future__ import annotations

import argparse
import csv
import json
import os
import re
import shutil
import subprocess
import sys
import time
from pathlib import Path
from typing import Dict, List, Tuple

import requests
from dotenv import load_dotenv

ROOT = Path(__file__).resolve().parents[1]
INPUT = ROOT / "input"
CONFIG = ROOT / "config"
SCENARIOS = ROOT / "scenarios"
OUTPUT = ROOT / "output"
SEGMENTS = OUTPUT / "segments"
FINAL = OUTPUT / "final"
LOGS = ROOT / "logs"

API_BASE = "https://api.elevenlabs.io/v1"


def ensure_dirs() -> None:
    for p in [OUTPUT, SEGMENTS, FINAL, LOGS]:
        p.mkdir(parents=True, exist_ok=True)


def load_api_key() -> str:
    load_dotenv(ROOT / ".env")
    key = os.getenv("ELEVENLABS_API_KEY", "").strip()
    if not key or key == "OVDJE_ZALIJEPI_SVOJ_API_KEY":
        raise SystemExit(
            "Nedostaje ELEVENLABS_API_KEY.\n"
            "Otvori .env datoteku i zalijepi API key u obliku:\n"
            "ELEVENLABS_API_KEY=sk_..."
        )
    return key


def headers(api_key: str) -> Dict[str, str]:
    return {
        "xi-api-key": api_key,
        "Content-Type": "application/json",
        "Accept": "audio/mpeg",
    }


def json_headers(api_key: str) -> Dict[str, str]:
    return {
        "xi-api-key": api_key,
        "Content-Type": "application/json",
    }


def get_voices(api_key: str) -> List[dict]:
    r = requests.get(f"{API_BASE}/voices", headers=json_headers(api_key), timeout=60)
    if r.status_code >= 400:
        raise RuntimeError(f"ElevenLabs /voices greška {r.status_code}: {r.text[:500]}")
    data = r.json()
    return data.get("voices", [])


def save_voices(api_key: str) -> None:
    voices = get_voices(api_key)
    out = OUTPUT / "voices_list.json"
    out.write_text(json.dumps(voices, ensure_ascii=False, indent=2), encoding="utf-8")
    print(f"Glasovi spremljeni: {out}")
    print("Prvih 10 glasova:")
    for v in voices[:10]:
        print(f"- {v.get('name')} | voice_id={v.get('voice_id')}")


def load_config(api_key: str) -> dict:
    cfg_path = CONFIG / "voices_config.json"
    cfg = json.loads(cfg_path.read_text(encoding="utf-8"))

    needs_auto = (
        cfg.get("auto_pick_voices_if_empty", True)
        and (not cfg["voice_host"].get("voice_id") or not cfg["voice_master"].get("voice_id"))
    )
    if needs_auto:
        voices = get_voices(api_key)
        usable = [v for v in voices if v.get("voice_id")]
        if len(usable) < 1:
            raise RuntimeError("Nema dostupnih glasova na ElevenLabs računu.")
        if not cfg["voice_host"].get("voice_id"):
            cfg["voice_host"]["voice_id"] = usable[0]["voice_id"]
            cfg["voice_host"]["label"] += f" ({usable[0].get('name', 'glas')})"
        if not cfg["voice_master"].get("voice_id"):
            pick = usable[1] if len(usable) > 1 else usable[0]
            cfg["voice_master"]["voice_id"] = pick["voice_id"]
            cfg["voice_master"]["label"] += f" ({pick.get('name', 'glas')})"

        print("Automatski odabrani glasovi:")
        print(f"- Voditelj: {cfg['voice_host']['voice_id']}")
        print(f"- Majstor: {cfg['voice_master']['voice_id']}")
        print("Kasnije ih možeš ručno zamijeniti u config/voices_config.json.")

    return cfg


def parse_scenario(path: Path) -> List[Tuple[str, str]]:
    text = path.read_text(encoding="utf-8")
    segments: List[Tuple[str, str]] = []
    pattern = re.compile(r"^\*\*(VODITELJ|MAJSTOR):\*\*\s*(.+)$", re.MULTILINE)
    for m in pattern.finditer(text):
        role = m.group(1).strip()
        line = re.sub(r"\s+", " ", m.group(2).strip())
        if line:
            segments.append((role, line))
    if not segments:
        raise RuntimeError(f"Nema govornih segmenata u {path}")
    return segments


def tts_segment(api_key: str, cfg: dict, role: str, text: str, out_file: Path) -> None:
    if out_file.exists() and out_file.stat().st_size > 1000:
        print(f"SKIP segment postoji: {out_file.name}")
        return

    role_cfg = cfg["voice_host"] if role == "VODITELJ" else cfg["voice_master"]
    voice_id = role_cfg["voice_id"]
    if not voice_id:
        raise RuntimeError(f"Nedostaje voice_id za {role}.")

    url = f"{API_BASE}/text-to-speech/{voice_id}"
    params = {"output_format": cfg.get("output_format", "mp3_44100_128")}
    payload = {
        "text": text,
        "model_id": cfg.get("model_id", "eleven_multilingual_v2"),
        "language_code": cfg.get("language_code", "hr"),
        "voice_settings": {
            "stability": role_cfg.get("stability", 0.55),
            "similarity_boost": role_cfg.get("similarity_boost", 0.75),
            "style": role_cfg.get("style", 0.1),
            "use_speaker_boost": role_cfg.get("use_speaker_boost", True),
        },
    }

    r = requests.post(url, headers=headers(api_key), params=params, json=payload, timeout=180)
    if r.status_code >= 400:
        raise RuntimeError(f"TTS greška {r.status_code}: {r.text[:800]}")

    out_file.write_bytes(r.content)
    print(f"OK segment: {out_file.name} ({len(text)} znakova)")
    time.sleep(0.35)


def ffmpeg_exists() -> bool:
    return shutil.which("ffmpeg") is not None


def merge_segments(segment_files: List[Path], final_file: Path) -> None:
    final_file.parent.mkdir(parents=True, exist_ok=True)
    concat_file = final_file.with_suffix(".concat.txt")
    concat_file.write_text(
        "\n".join(f"file '{p.as_posix()}'" for p in segment_files),
        encoding="utf-8"
    )

    if not ffmpeg_exists():
        raise RuntimeError(
            "FFmpeg nije pronađen u PATH-u. Segmenti su generirani, ali nisu spojeni.\n"
            f"Concat lista je ovdje: {concat_file}"
        )

    cmd = [
        "ffmpeg", "-y",
        "-f", "concat", "-safe", "0",
        "-i", str(concat_file),
        "-c", "copy",
        str(final_file),
    ]
    subprocess.run(cmd, check=True)
    print(f"FINAL MP3: {final_file}")


def render_episode(api_key: str, cfg: dict, episode: dict) -> dict:
    episode_id = episode["episode_id"]
    scenario_path = ROOT / episode["scenario_file"]
    segs = parse_scenario(scenario_path)
    ep_seg_dir = SEGMENTS / episode_id
    ep_seg_dir.mkdir(parents=True, exist_ok=True)

    segment_files: List[Path] = []
    for idx, (role, text) in enumerate(segs, start=1):
        out = ep_seg_dir / f"{episode_id}_{idx:03d}_{role.lower()}.mp3"
        tts_segment(api_key, cfg, role, text, out)
        segment_files.append(out)

    final_file = ROOT / episode["output_file"]
    merge_segments(segment_files, final_file)

    return {
        "episode_id": episode_id,
        "title": episode["title"],
        "slug": episode["slug"],
        "description": episode["description"],
        "file": str(final_file.relative_to(ROOT)),
        "segments": len(segment_files),
        "articles": ", ".join(episode.get("articles", [])),
    }


def build_wp_csv(rows: List[dict]) -> None:
    out = OUTPUT / "wordpress_podcast_opisi.csv"
    with out.open("w", encoding="utf-8-sig", newline="") as f:
        writer = csv.DictWriter(
            f,
            fieldnames=["episode_id", "title", "slug", "description", "file", "segments", "articles"]
        )
        writer.writeheader()
        writer.writerows(rows)
    print(f"WordPress CSV: {out}")


def main() -> None:
    parser = argparse.ArgumentParser()
    parser.add_argument("--list-voices", action="store_true", help="Dohvati i spremi popis ElevenLabs glasova.")
    parser.add_argument("--render-all", action="store_true", help="Generiraj svih 5 epizoda.")
    parser.add_argument("--episode", help="Generiraj samo jednu epizodu, npr. EP01.")
    args = parser.parse_args()

    ensure_dirs()
    api_key = load_api_key()

    if args.list_voices:
        save_voices(api_key)
        return

    cfg = load_config(api_key)
    manifest = json.loads((INPUT / "podcast_manifest.json").read_text(encoding="utf-8"))

    if args.episode:
        manifest = [e for e in manifest if e["episode_id"].upper() == args.episode.upper()]
        if not manifest:
            raise SystemExit(f"Nema epizode: {args.episode}")

    if not args.render_all and not args.episode:
        print("Nema zadatka. Koristi --render-all, --episode EP01 ili --list-voices.")
        return

    rows = []
    for ep in manifest:
        print("=" * 60)
        print(f"Generiram {ep['episode_id']} — {ep['title']}")
        print("=" * 60)
        rows.append(render_episode(api_key, cfg, ep))

    build_wp_csv(rows)
    print("GOTOVO.")


if __name__ == "__main__":
    main()

# -*- coding: utf-8 -*-
"""
Drycured Podcast Engine — audicija spremljenih ElevenLabs glasova.

Za razliku od stare skripte koja je čitala /v1/voices, ova koristi /v2/voices
s voice_type=saved i non-default, jer se glasovi iz Voice Libraryja često vide
kao spremljeni/saved ili community glasovi.
"""

from pathlib import Path
import csv
import json
import os
import re
import time
from urllib.parse import urlencode

import requests
from dotenv import load_dotenv

ROOT = Path(__file__).resolve().parents[1]
OUTPUT = ROOT / "output"
AUDITION = OUTPUT / "audicija_spremljenih_glasova"
CONFIG = ROOT / "config"

API_BASE = "https://api.elevenlabs.io/v1"
API_BASE_V2 = "https://api.elevenlabs.io/v2"

TEST_TEXT = (
    "Dobrodošli u Drycured podcast. Danas govorimo o sušenju, dimljenju i zrenju "
    "suhomesnatih proizvoda. Dobar proizvod ne nastaje iz žurbe, nego iz pravilnog "
    "odnosa mesa, soli, zraka i vremena. Ako se kobasica suši prebrzo, površina se "
    "zatvara, a sredina ostaje mekana i nesigurna."
)

def safe_name(text):
    text = (text or "glas").lower().strip()
    for src, dst in {"č":"c","ć":"c","š":"s","ž":"z","đ":"d"," ":"_","-":"_"}.items():
        text = text.replace(src, dst)
    text = re.sub(r"[^a-z0-9_]+", "", text)
    text = re.sub(r"_+", "_", text).strip("_")
    return text[:55] or "glas"

def load_key():
    load_dotenv(ROOT / ".env")
    key = os.getenv("ELEVENLABS_API_KEY", "").strip()
    if not key:
        raise SystemExit("Nedostaje ELEVENLABS_API_KEY u .env datoteci.")
    return key

def api_get(api_key, url, params=None):
    r = requests.get(
        url,
        headers={"xi-api-key": api_key, "Content-Type": "application/json"},
        params=params or {},
        timeout=60,
    )
    if r.status_code >= 400:
        raise RuntimeError(f"GET greška {r.status_code}: {r.text[:800]}")
    return r.json()

def list_voices_v2(api_key, voice_type):
    voices = []
    token = None
    while True:
        params = {
            "page_size": 100,
            "voice_type": voice_type,
            "include_total_count": "true",
            "sort": "name",
            "sort_direction": "asc",
        }
        if token:
            params["next_page_token"] = token
        data = api_get(api_key, f"{API_BASE_V2}/voices", params=params)
        voices.extend(data.get("voices", []))
        if not data.get("has_more"):
            break
        token = data.get("next_page_token")
        if not token:
            break
    return voices

def load_model_settings():
    cfg_path = CONFIG / "voices_config.json"
    if not cfg_path.exists():
        return "eleven_multilingual_v2", "hr", "mp3_44100_128"
    cfg = json.loads(cfg_path.read_text(encoding="utf-8"))
    return (
        cfg.get("model_id", "eleven_multilingual_v2"),
        cfg.get("language_code", "hr"),
        cfg.get("output_format", "mp3_44100_128"),
    )

def tts(api_key, voice_id, text, out_file, model_id, language_code, output_format):
    if out_file.exists() and out_file.stat().st_size > 1000:
        print(f"SKIP postoji: {out_file.name}")
        return "SKIP"

    payload = {
        "text": text,
        "model_id": model_id,
        "language_code": language_code,
        "voice_settings": {
            "stability": 0.45,
            "similarity_boost": 0.78,
            "style": 0.10,
            "use_speaker_boost": True
        }
    }
    r = requests.post(
        f"{API_BASE}/text-to-speech/{voice_id}",
        headers={
            "xi-api-key": api_key,
            "Content-Type": "application/json",
            "Accept": "audio/mpeg",
        },
        params={"output_format": output_format},
        json=payload,
        timeout=180,
    )
    if r.status_code >= 400:
        print(f"FAILED {voice_id}: {r.status_code} {r.text[:400]}")
        return f"FAILED {r.status_code}"

    out_file.write_bytes(r.content)
    print(f"OK: {out_file.name}")
    time.sleep(0.35)
    return "OK"

def main():
    AUDITION.mkdir(parents=True, exist_ok=True)
    api_key = load_key()
    model_id, language_code, output_format = load_model_settings()

    saved = list_voices_v2(api_key, "saved")
    non_default = list_voices_v2(api_key, "non-default")

    by_id = {}
    for v in saved + non_default:
        vid = v.get("voice_id")
        if vid:
            by_id[vid] = v

    voices = list(by_id.values())

    if not voices:
        raise SystemExit(
            "Nisam našao spremljene glasove preko API-ja.\n"
            "Provjeri u ElevenLabsu jesu li glasovi dodani u My Voices/Voice Collection, "
            "a ne samo označeni kao favorit/bookmark."
        )

    raw_path = AUDITION / "RAW_V2_VOICES.json"
    raw_path.write_text(json.dumps(voices, ensure_ascii=False, indent=2), encoding="utf-8")

    rows = []
    print("=" * 60)
    print("Drycured audicija SPREMLJENIH glasova")
    print("=" * 60)
    print(f"Pronađeno glasova: {len(voices)}")

    for i, v in enumerate(voices, start=1):
        name = v.get("name", f"glas_{i}")
        voice_id = v.get("voice_id", "")
        category = v.get("category", "")
        labels = v.get("labels") or {}
        if not voice_id:
            continue

        filename = f"{i:02d}_{safe_name(name)}_{voice_id[:8]}.mp3"
        out_file = AUDITION / filename
        status = tts(api_key, voice_id, TEST_TEXT, out_file, model_id, language_code, output_format)

        rows.append({
            "redni_broj": i,
            "name": name,
            "voice_id": voice_id,
            "category": category,
            "gender": labels.get("gender", ""),
            "age": labels.get("age", ""),
            "accent": labels.get("accent", ""),
            "description": labels.get("description", "") or v.get("description", ""),
            "file": str(out_file.relative_to(ROOT)),
            "status": status,
        })

    csv_path = AUDITION / "AUDICIJA_SPREMLJENIH_GLASOVA_INDEX.csv"
    with csv_path.open("w", encoding="utf-8-sig", newline="") as f:
        writer = csv.DictWriter(
            f,
            fieldnames=["redni_broj", "name", "voice_id", "category", "gender", "age", "accent", "description", "file", "status"]
        )
        writer.writeheader()
        writer.writerows(rows)

    print("=" * 60)
    print("Gotovo.")
    print(f"Preslušaj MP3 uzorke u: {AUDITION}")
    print(f"Indeks: {csv_path}")
    print("=" * 60)

if __name__ == "__main__":
    main()

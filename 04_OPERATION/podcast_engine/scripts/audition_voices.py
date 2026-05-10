# -*- coding: utf-8 -*-
"""
Audicija glasova za Drycured Podcast Engine.
Generira isti kratki hrvatski MP3 uzorak za svaki glas iz ElevenLabs kolekcije.
"""

from pathlib import Path
import csv
import json
import os
import re
import time

import requests
from dotenv import load_dotenv

ROOT = Path(__file__).resolve().parents[1]
OUTPUT = ROOT / "output"
AUDITION = OUTPUT / "audicija_glasova"
CONFIG = ROOT / "config"

API_BASE = "https://api.elevenlabs.io/v1"

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

def get_voices(api_key):
    r = requests.get(
        f"{API_BASE}/voices",
        headers={"xi-api-key": api_key, "Content-Type": "application/json"},
        timeout=60,
    )
    if r.status_code >= 400:
        raise RuntimeError(f"Greška pri dohvatu glasova {r.status_code}: {r.text[:600]}")
    return r.json().get("voices", [])

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
        print(f"FAILED {voice_id}: {r.status_code} {r.text[:300]}")
        return f"FAILED {r.status_code}"

    out_file.write_bytes(r.content)
    print(f"OK: {out_file.name}")
    time.sleep(0.35)
    return "OK"

def main():
    AUDITION.mkdir(parents=True, exist_ok=True)
    api_key = load_key()
    model_id, language_code, output_format = load_model_settings()
    voices = get_voices(api_key)

    if not voices:
        raise SystemExit("Nema glasova u ElevenLabs kolekciji. Dodaj glasove u My Voices.")

    rows = []
    print("=" * 60)
    print("Drycured audicija glasova")
    print("=" * 60)

    for i, v in enumerate(voices, start=1):
        name = v.get("name", f"glas_{i}")
        voice_id = v.get("voice_id", "")
        if not voice_id:
            continue

        filename = f"{i:02d}_{safe_name(name)}_{voice_id[:8]}.mp3"
        out_file = AUDITION / filename
        status = tts(api_key, voice_id, TEST_TEXT, out_file, model_id, language_code, output_format)

        rows.append({
            "redni_broj": i,
            "name": name,
            "voice_id": voice_id,
            "file": str(out_file.relative_to(ROOT)),
            "status": status,
        })

    csv_path = AUDITION / "AUDICIJA_GLASOVA_INDEX.csv"
    with csv_path.open("w", encoding="utf-8-sig", newline="") as f:
        writer = csv.DictWriter(f, fieldnames=["redni_broj", "name", "voice_id", "file", "status"])
        writer.writeheader()
        writer.writerows(rows)

    json_path = AUDITION / "AUDICIJA_GLASOVA_INDEX.json"
    json_path.write_text(json.dumps(rows, ensure_ascii=False, indent=2), encoding="utf-8")

    print("=" * 60)
    print("Gotovo.")
    print(f"Preslušaj MP3 uzorke u: {AUDITION}")
    print(f"Indeks glasova: {csv_path}")
    print("=" * 60)

if __name__ == "__main__":
    main()

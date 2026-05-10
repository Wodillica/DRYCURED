# -*- coding: utf-8 -*-
from pathlib import Path
import json
import sys

if len(sys.argv) < 3:
    raise SystemExit("Upotreba: python scripts/set_voices_config.py HOST_VOICE_ID MASTER_VOICE_ID")

root = Path(__file__).resolve().parents[1]
cfg_path = root / "config" / "voices_config.json"
cfg = json.loads(cfg_path.read_text(encoding="utf-8"))

host_id = sys.argv[1].strip()
master_id = sys.argv[2].strip()

if not host_id or not master_id:
    raise SystemExit("Voice ID ne smije biti prazan.")

cfg["voice_host"]["voice_id"] = host_id
cfg["voice_master"]["voice_id"] = master_id
cfg["auto_pick_voices_if_empty"] = False

cfg["voice_host"]["stability"] = 0.45
cfg["voice_host"]["similarity_boost"] = 0.78
cfg["voice_host"]["style"] = 0.10
cfg["voice_host"]["use_speaker_boost"] = True

cfg["voice_master"]["stability"] = 0.50
cfg["voice_master"]["similarity_boost"] = 0.80
cfg["voice_master"]["style"] = 0.14
cfg["voice_master"]["use_speaker_boost"] = True

cfg_path.write_text(json.dumps(cfg, ensure_ascii=False, indent=2), encoding="utf-8")

print("Glasovi su zaključani.")
print("Voditelj:", host_id)
print("Majstor:", master_id)

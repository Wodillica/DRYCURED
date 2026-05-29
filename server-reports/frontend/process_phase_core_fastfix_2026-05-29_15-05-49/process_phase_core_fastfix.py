from pathlib import Path
import re
import shutil
import subprocess
import datetime
import os

BASE = Path("/var/www/html")
MU = BASE / "wp-content/mu-plugins"
REPORT = Path(os.environ["REPORT_DIR"])
BACKUP = REPORT / "backups"
BACKUP.mkdir(parents=True, exist_ok=True)

slugs = [
    "sirovina",
    "soljenje",
    "rezanje",
    "mijesanje",
    "odlezavanje",
    "punjenje",
    "dimljenje",
    "susenje",
    "zrenje",
    "pakiranje",
]

def backup_file(path):
    dst = BACKUP / path.name
    shutil.copy2(path, dst)
    return dst

def php_lint(path):
    r = subprocess.run(["php", "-l", str(path)], text=True, capture_output=True)
    return r.returncode, r.stdout.strip(), r.stderr.strip()

log = []
log.append("=== DRYCURED PROCESS CORE FASTFIX v001 ===")
log.append(datetime.datetime.utcnow().isoformat() + "Z")
log.append("")

for slug in slugs:
    log.append(f"--- PHASE: {slug} ---")
    core = MU / f"drycured-process-{slug}-core.php"

    if not core.exists():
        log.append(f"WARNING: core file not found: {core}")
        log.append("")
        continue

    text = core.read_text(encoding="utf-8", errors="replace")
    original = text

    log.append(f"CORE: {core}")

    backup_file(core)

    # 1) return $content . do_shortcode('[shortcode]');
    text = re.sub(
        r"return\s+\$content\s*\.\s*do_shortcode\((\s*'[^']+'\s*)\);",
        r"return do_shortcode(\1);",
        text,
        count=1
    )

    # 2) return $content . $html;
    text = re.sub(
        r"return\s+\$content\s*\.\s*(\$[a-zA-Z_][a-zA-Z0-9_]*)\s*;",
        r"return \1;",
        text,
        count=1
    )

    # 3) return $content . funkcija();
    text = re.sub(
        r"return\s+\$content\s*\.\s*([a-zA-Z_][a-zA-Z0-9_]*\([^;]*\))\s*;",
        r"return \1;",
        text,
        count=1
    )

    if text != original:
        core.write_text(text, encoding="utf-8")
        code, out, err = php_lint(core)
        log.append("PATCH: old content disabled, modern block remains")
        log.append(f"PHP_LINT: code={code} {out} {err}")
        if code != 0:
            backup = BACKUP / core.name
            shutil.copy2(backup, core)
            log.append("ERROR: lint failed; file restored from backup")
    else:
        log.append("NOCHANGE: no known $content append pattern found")

    log.append("")

(REPORT / "core_fastfix_report.txt").write_text("\n".join(log), encoding="utf-8")
print("\n".join(log))

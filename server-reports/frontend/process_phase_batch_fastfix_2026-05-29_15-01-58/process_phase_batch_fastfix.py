from pathlib import Path
import re
import shutil
import subprocess
import datetime

BASE = Path("/var/www/html")
MU = BASE / "wp-content/mu-plugins"
IMG_DIR = BASE / "wp-content/uploads/drycured/home-process"
REPORT = Path(__import__("os").environ["REPORT_DIR"])
BACKUP = REPORT / "backups"
BACKUP.mkdir(parents=True, exist_ok=True)

# Fermentacija je već pilot; uključujemo je samo radi validacije, ali patch fokus je na svim fazama.
phase_order = {
    "sirovina": "01",
    "soljenje": "02",
    "rezanje": "03",
    "mljevenje": "04",
    "mijesanje": "05",
    "odlezavanje": "5a",
    "punjenje": "06",
    "fermentacija": "07",
    "dimljenje": "08",
    "susenje": "09",
    "zrenje": "10",
    "pakiranje": "11",
}

def find_image(slug, num):
    patterns = [
        f"process-{num}-{slug}.webp",
        f"process-{num}-{slug}.png",
        f"process-*{slug}*.webp",
        f"*{slug}*.webp",
        f"*{slug}*.jpg",
        f"*{slug}*.png",
    ]
    for pat in patterns:
        found = sorted(IMG_DIR.glob(pat))
        if found:
            return found[0]
    return None

def backup_file(path):
    rel = path.relative_to(BASE)
    dst = BACKUP / str(rel).replace("/", "__")
    shutil.copy2(path, dst)
    return dst

def patch_content_return(path):
    text = path.read_text(encoding="utf-8", errors="replace")
    original = text

    # Najčešći obrazac:
    # return $content . do_shortcode('[shortcode]');
    text = re.sub(
        r"return\s+\$content\s*\.\s*do_shortcode\((\s*'[^']+'\s*)\);",
        r"return do_shortcode(\1);",
        text,
        count=1
    )

    # Drugi mogući obrazac:
    # return $content . funkcija();
    # Ne diramo agresivno ako nema shortcodea.

    if text != original:
        path.write_text(text, encoding="utf-8")
        return True
    return False

def patch_top_polish_image(path, image_url):
    text = path.read_text(encoding="utf-8", errors="replace")
    original = text

    # Obrazac koji smo dokazali na fermentaciji:
    # const imgSrc = img ? (...) : '';
    text = re.sub(
        r"const\s+imgSrc\s*=\s*img\s*\?\s*\((.*?)\)\s*:\s*''\s*;",
        "const imgSrc = img ? (\\1) : '" + image_url + "';",
        text,
        count=1,
        flags=re.S
    )

    # Ako već postoji fallback, ali prazan ili drugačiji, ne forsiramo drugi put.
    if text != original:
        path.write_text(text, encoding="utf-8")
        return True

    return False

def php_lint(path):
    r = subprocess.run(["php", "-l", str(path)], text=True, capture_output=True)
    return r.returncode, r.stdout.strip(), r.stderr.strip()

log = []
log.append("=== DRYCURED PROCESS PHASE BATCH FASTFIX v001 ===")
log.append(datetime.datetime.utcnow().isoformat() + "Z")
log.append("")

for slug, num in phase_order.items():
    log.append(f"--- PHASE: {slug} ---")

    image = find_image(slug, num)
    if image:
        image_url = "https://drycured.com" + str(image).replace(str(BASE), "")
        log.append(f"IMAGE: {image}")
        log.append(f"IMAGE_URL: {image_url}")
    else:
        image_url = ""
        log.append("WARNING: image not found; image fallback patch skipped")

    module_dir = MU / f"drycured-process-{slug}-modules"
    core_file = MU / f"drycured-process-{slug}-core.php"

    if not module_dir.exists():
        log.append(f"WARNING: module dir not found: {module_dir}")
        continue

    files = sorted(module_dir.glob("*.php"))

    content_candidates = [
        p for p in files
        if "content" in p.name.lower()
    ]

    top_candidates = [
        p for p in files
        if "top-polish" in p.name.lower() or "top" in p.name.lower() or "fine-tune" in p.name.lower()
    ]

    changed = []

    for p in content_candidates:
        txt = p.read_text(encoding="utf-8", errors="replace")
        if "$content" in txt and "do_shortcode" in txt:
            backup_file(p)
            if patch_content_return(p):
                changed.append(p)
                log.append(f"PATCH content return: {p}")
            else:
                log.append(f"NOCHANGE content return: {p}")

    if image_url:
        for p in top_candidates:
            txt = p.read_text(encoding="utf-8", errors="replace")
            if "imgSrc" in txt and "<img" in txt:
                backup_file(p)
                if patch_top_polish_image(p, image_url):
                    changed.append(p)
                    log.append(f"PATCH image fallback: {p}")
                else:
                    log.append(f"NOCHANGE image fallback: {p}")

    # Syntax check all changed files
    for p in changed:
        code, out, err = php_lint(p)
        log.append(f"PHP_LINT {p}: code={code} {out} {err}")
        if code != 0:
            log.append(f"ERROR: PHP lint failed for {p}; restore from backup manually before continuing.")

    if not changed:
        log.append("NO PATCH APPLIED for this phase")

    log.append("")

(REPORT / "batch_fastfix_report.txt").write_text("\n".join(log), encoding="utf-8")
print("\n".join(log))

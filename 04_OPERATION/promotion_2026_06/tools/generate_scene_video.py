#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Drycured Scene Video Generator v3.

Builds a local 1080x1920 H.264/AAC video from five prepared scene images and
one voiceover MP3. FFmpeg is used for all final video and preview rendering.
No cloud/API tools are used.
"""

from __future__ import annotations

import argparse
import shutil
import subprocess
import sys
from datetime import datetime
from pathlib import Path


WIDTH = 1080
HEIGHT = 1920
FPS = 30
VIDEO_ID = "VID-001"
FONT_FILE = Path("C:/Windows/Fonts/arial.ttf")
FFMPEG_FONT = "C\\:/Windows/Fonts/arial.ttf"
ACCENT = "0xD39A3A"

SCENES = [
    {
        "image": "VID-001_scene_01.png",
        "duration": 5,
        "caption": "Zašto se kobasica ne smije sušiti prebrzo?",
    },
    {
        "image": "VID-001_scene_02.png",
        "duration": 7,
        "caption": "Površina se zatvori, a voda ostane u sredini.",
    },
    {
        "image": "VID-001_scene_03.png",
        "duration": 7,
        "caption": "Tvrda kora. Mekana jezgra. Rizik za šaržu.",
    },
    {
        "image": "VID-001_scene_04.png",
        "duration": 8,
        "caption": "Manje propuha, više kontrole vlage i sporije sušenje.",
    },
    {
        "image": "VID-001_scene_05.png",
        "duration": 5,
        "caption": "Drycured.com — Digitalna pušnica znanja.",
    },
]


def project_root() -> Path:
    return Path(__file__).resolve().parents[1]


def paths(root: Path) -> dict[str, Path]:
    return {
        "scene_dir": root / "01_VIDEO_SHORTS" / "input" / "images" / VIDEO_ID,
        "audio": root / "01_VIDEO_SHORTS" / "input" / "audio" / VIDEO_ID / f"{VIDEO_ID}_voice.mp3",
        "videos": root / "01_VIDEO_SHORTS" / "output" / "videos",
        "previews": root / "01_VIDEO_SHORTS" / "output" / "previews",
        "logs": root / "01_VIDEO_SHORTS" / "logs",
        "output": root / "01_VIDEO_SHORTS" / "output" / "videos" / f"{VIDEO_ID}_SCENE_VIDEO_V3.mp4",
        "preview": root / "01_VIDEO_SHORTS" / "output" / "previews" / f"{VIDEO_ID}_scene_preview.png",
        "debug": root / "01_VIDEO_SHORTS" / "logs" / f"{VIDEO_ID}_scene_video_debug.log",
    }


def ensure_dirs(p: dict[str, Path]) -> None:
    for key in ["videos", "previews", "logs"]:
        p[key].mkdir(parents=True, exist_ok=True)


def write_log(log_path: Path, lines: list[str]) -> None:
    log_path.parent.mkdir(parents=True, exist_ok=True)
    log_path.write_text("\n".join(lines), encoding="utf-8")


def append_log(log_path: Path, lines: list[str]) -> None:
    with log_path.open("a", encoding="utf-8") as handle:
        handle.write("\n".join(lines))
        handle.write("\n")


def find_missing(ffmpeg: str | None, p: dict[str, Path]) -> list[str]:
    missing: list[str] = []
    if not ffmpeg:
        missing.append("FFmpeg nije pronađen u PATH-u.")
    if not FONT_FILE.exists():
        missing.append(f"Font nije pronađen: {FONT_FILE}")
    for scene in SCENES:
        image_path = p["scene_dir"] / scene["image"]
        if not image_path.exists():
            missing.append(f"Slika nije pronađena: {image_path}")
    if not p["audio"].exists():
        missing.append(f"Voiceover nije pronađen: {p['audio']}")
    return missing


def ff_escape(value: str) -> str:
    return (
        value.replace("\\", "/")
        .replace(":", "\\:")
        .replace("'", "\\'")
        .replace(",", "\\,")
        .replace("[", "\\[")
        .replace("]", "\\]")
    )


def caption_file(root: Path, index: int, caption: str) -> Path:
    out = root / "01_VIDEO_SHORTS" / "logs" / f"{VIDEO_ID}_scene_{index:02d}_caption.txt"
    out.write_text(caption, encoding="utf-8")
    return out


def scene_filter(index: int, duration: int, caption_path: Path, root: Path) -> str:
    frames = duration * FPS
    fade_out_start = max(0, duration - 0.45)
    caption = ff_escape(caption_path.relative_to(root).as_posix())
    return (
        f"[{index}:v]split=2[bg{index}src][fg{index}src];"
        f"[bg{index}src]scale={WIDTH}:{HEIGHT}:force_original_aspect_ratio=increase,"
        f"crop={WIDTH}:{HEIGHT},gblur=sigma=28,setsar=1,"
        f"drawbox=x=0:y=0:w=iw:h=ih:color=black@0.36:t=fill[bg{index}];"
        f"[fg{index}src]scale={WIDTH}:{HEIGHT}:force_original_aspect_ratio=decrease,"
        f"setsar=1[fg{index}];"
        f"[bg{index}][fg{index}]overlay=(W-w)/2:(H-h)/2,"
        f"zoompan=z='1+0.018*on/{frames}':x='iw/2-(iw/zoom/2)':y='ih/2-(ih/zoom/2)':"
        f"d=1:s={WIDTH}x{HEIGHT}:fps={FPS},"
        f"drawbox=x=92:y=1510:w=896:h=172:color=black@0.30:t=fill,"
        f"drawbox=x=122:y=1510:w=6:h=172:color={ACCENT}@0.88:t=fill,"
        f"drawtext=fontfile='{FFMPEG_FONT}':textfile='{caption}':"
        f"fontcolor=white:fontsize=43:line_spacing=8:x=154:y=1562:"
        f"shadowcolor=black@0.32:shadowx=0:shadowy=2,"
        f"fade=t=in:st=0:d=0.32,fade=t=out:st={fade_out_start}:d=0.45,"
        f"format=yuv420p[v{index}]"
    )


def build_video_command(ffmpeg: str, root: Path, p: dict[str, Path]) -> list[str]:
    caption_paths = [caption_file(root, i, scene["caption"]) for i, scene in enumerate(SCENES, start=1)]

    cmd = [ffmpeg, "-y", "-hide_banner"]
    for scene in SCENES:
        cmd.extend(
            [
                "-loop",
                "1",
                "-framerate",
                str(FPS),
                "-t",
                str(scene["duration"]),
                "-i",
                str(p["scene_dir"] / scene["image"]),
            ]
        )
    cmd.extend(["-i", str(p["audio"])])

    filters = [
        scene_filter(index, int(scene["duration"]), caption_paths[index], root)
        for index, scene in enumerate(SCENES)
    ]
    concat_inputs = "".join(f"[v{i}]" for i in range(len(SCENES)))
    total_duration = sum(int(scene["duration"]) for scene in SCENES)
    filters.append(f"{concat_inputs}concat=n={len(SCENES)}:v=1:a=0[vcat]")
    filters.append(
        f"[5:a]atrim=0:{total_duration},asetpts=PTS-STARTPTS,"
        "dynaudnorm=f=150:g=9,"
        "aformat=sample_fmts=fltp:sample_rates=44100:channel_layouts=stereo[aout]"
    )

    cmd.extend(
        [
            "-filter_complex",
            ";".join(filters),
            "-map",
            "[vcat]",
            "-map",
            "[aout]",
            "-r",
            str(FPS),
            "-c:v",
            "libx264",
            "-pix_fmt",
            "yuv420p",
            "-c:a",
            "aac",
            "-b:a",
            "192k",
            "-movflags",
            "+faststart",
            "-t",
            str(total_duration),
            str(p["output"]),
        ]
    )
    return cmd


def build_preview_command(ffmpeg: str, p: dict[str, Path]) -> list[str]:
    cmd = [ffmpeg, "-y", "-hide_banner"]
    for scene in SCENES:
        cmd.extend(["-i", str(p["scene_dir"] / scene["image"])])

    parts = []
    for index in range(len(SCENES)):
        parts.append(
            f"[{index}:v]scale=216:384:force_original_aspect_ratio=decrease,"
            f"pad=216:384:(ow-iw)/2:(oh-ih)/2:color=0x15110c,"
            f"drawbox=x=0:y=340:w=216:h=44:color=black@0.42:t=fill,"
            f"drawtext=fontfile='{FFMPEG_FONT}':text='SCENA {index + 1}':"
            f"fontcolor=white:fontsize=18:x=12:y=354[s{index}]"
        )
    layout = "|".join(f"{216 * index}_0" for index in range(len(SCENES)))
    parts.append("".join(f"[s{index}]" for index in range(len(SCENES))) + f"xstack=inputs=5:layout={layout}[out]")

    cmd.extend(["-filter_complex", ";".join(parts), "-map", "[out]", "-frames:v", "1", str(p["preview"])])
    return cmd


def run_command(cmd: list[str], cwd: Path, log_path: Path, label: str) -> subprocess.CompletedProcess[str]:
    append_log(
        log_path,
        [
            "",
            f"{label}_COMMAND:",
            subprocess.list2cmdline(cmd),
            "",
        ],
    )
    completed = subprocess.run(
        cmd,
        cwd=str(cwd),
        text=True,
        stdout=subprocess.PIPE,
        stderr=subprocess.PIPE,
        encoding="utf-8",
        errors="replace",
    )
    append_log(
        log_path,
        [
            f"{label}_RETURN_CODE: {completed.returncode}",
            f"{label}_STDOUT:",
            completed.stdout,
            f"{label}_STDERR:",
            completed.stderr,
        ],
    )
    return completed


def render() -> int:
    root = project_root()
    p = paths(root)
    ensure_dirs(p)
    ffmpeg = shutil.which("ffmpeg")
    total_duration = sum(int(scene["duration"]) for scene in SCENES)

    write_log(
        p["debug"],
        [
            "DRYCURED SCENE VIDEO V3 DEBUG LOG",
            f"timestamp={datetime.now():%Y-%m-%d %H:%M:%S}",
            f"root={root}",
            f"output={p['output']}",
            f"preview={p['preview']}",
            f"audio={p['audio']}",
            f"duration_seconds={total_duration}",
        ],
    )

    missing = find_missing(ffmpeg, p)
    if missing:
        append_log(p["debug"], ["", "PREFLIGHT_MISSING:", *missing])
        for item in missing:
            print(item, file=sys.stderr)
        print(f"Debug log: {p['debug']}", file=sys.stderr)
        return 1

    preview_cmd = build_preview_command(ffmpeg, p)
    preview_result = run_command(preview_cmd, root, p["debug"], "PREVIEW")
    if preview_result.returncode != 0:
        print(f"Preview render nije uspio. Debug log: {p['debug']}", file=sys.stderr)
        return 1

    video_cmd = build_video_command(ffmpeg, root, p)
    video_result = run_command(video_cmd, root, p["debug"], "VIDEO")
    if video_result.returncode != 0:
        print(f"Video render nije uspio. Debug log: {p['debug']}", file=sys.stderr)
        return 1

    if not p["output"].exists() or p["output"].stat().st_size < 1000:
        append_log(p["debug"], ["", f"OUTPUT_MISSING_OR_SMALL: {p['output']}"])
        print(f"MP4 nije valjano generiran: {p['output']}", file=sys.stderr)
        print(f"Debug log: {p['debug']}", file=sys.stderr)
        return 1

    print(f"MP4 generiran: {p['output']}")
    print(f"Preview generiran: {p['preview']}")
    print(f"Trajanje: {total_duration} s")
    print(f"Debug log: {p['debug']}")
    return 0


def main() -> int:
    parser = argparse.ArgumentParser(description="Drycured scene video generator v3")
    parser.add_argument("--id", default=VIDEO_ID, help="Trenutno podržano: VID-001")
    args = parser.parse_args()
    if args.id.upper() != VIDEO_ID:
        print(f"Ovaj v3 generator trenutno podržava samo {VIDEO_ID}.", file=sys.stderr)
        return 1
    return render()


if __name__ == "__main__":
    raise SystemExit(main())

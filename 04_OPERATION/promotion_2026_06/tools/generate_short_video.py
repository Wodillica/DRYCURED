#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Drycured Short Video Factory.

Local 9:16 MP4 generator driven by JSON scenario files.
Final rendering is done with FFmpeg; no cloud services or paid APIs are used.
"""

from __future__ import annotations

import argparse
import json
import shutil
import subprocess
import sys
from datetime import datetime
from pathlib import Path


WIDTH = 1080
HEIGHT = 1920
FPS = 30
FONT_FILE = "C\\:/Windows/Fonts/arial.ttf"


def project_root() -> Path:
    return Path(__file__).resolve().parents[1]


def ensure_structure(root: Path) -> None:
    folders = [
        "00_PLAN",
        "01_VIDEO_SHORTS/input/images",
        "01_VIDEO_SHORTS/input/audio",
        "01_VIDEO_SHORTS/input/music",
        "01_VIDEO_SHORTS/input/scripts",
        "01_VIDEO_SHORTS/output/videos",
        "01_VIDEO_SHORTS/output/previews",
        "01_VIDEO_SHORTS/logs",
        "02_FACEBOOK",
        "03_PRESENTATION",
        "04_ASSETS",
        "05_EXPORTS",
        "06_ARCHIVE",
        "tools",
    ]
    for folder in folders:
        (root / folder).mkdir(parents=True, exist_ok=True)


def require_ffmpeg() -> str:
    ffmpeg = shutil.which("ffmpeg")
    if not ffmpeg:
        raise RuntimeError("FFmpeg nije pronađen. Instaliraj FFmpeg ili dodaj ffmpeg.exe u PATH.")
    return ffmpeg


def load_scenario(path: Path) -> dict:
    with path.open("r", encoding="utf-8") as handle:
        data = json.load(handle)
    required = ["id", "title", "slug", "duration_seconds", "image", "screens"]
    missing = [key for key in required if key not in data]
    if missing:
        raise ValueError(f"JSON scenarij nema obavezna polja: {', '.join(missing)}")
    return data


def parse_resolution(style: dict) -> tuple[int, int]:
    raw = str(style.get("resolution", f"{WIDTH}x{HEIGHT}")).lower()
    if raw != f"{WIDTH}x{HEIGHT}":
        print(f"Upozorenje: alat izvozi {WIDTH}x{HEIGHT}; JSON vrijednost '{raw}' se ignorira.")
    return WIDTH, HEIGHT


def ff_filter_escape(value: str) -> str:
    return (
        value.replace("\\", "/")
        .replace(":", "\\:")
        .replace("'", "\\'")
        .replace(",", "\\,")
        .replace("[", "\\[")
        .replace("]", "\\]")
    )


def ffmpeg_color(value: str) -> str:
    if value.startswith("#") and len(value) == 7:
        return "0x" + value[1:]
    return value


def rounded_alpha_expression(radius: int) -> str:
    return (
        f"if(lte(X,{radius})*lte(Y,{radius}),"
        f"if(lte(hypot({radius}-X,{radius}-Y),{radius}),255,0),"
        f"if(gte(X,W-{radius})*lte(Y,{radius}),"
        f"if(lte(hypot(X-(W-{radius}),{radius}-Y),{radius}),255,0),"
        f"if(lte(X,{radius})*gte(Y,H-{radius}),"
        f"if(lte(hypot({radius}-X,Y-(H-{radius})),{radius}),255,0),"
        f"if(gte(X,W-{radius})*gte(Y,H-{radius}),"
        f"if(lte(hypot(X-(W-{radius}),Y-(H-{radius})),{radius}),255,0),255))))"
    )


def write_text_files(root: Path, scenario: dict) -> list[Path]:
    log_dir = root / "01_VIDEO_SHORTS" / "logs"
    paths: list[Path] = []
    for index, screen in enumerate(scenario.get("screens", []), start=1):
        text = str(screen.get("text", "")).strip()
        text_path = log_dir / f"{scenario['id']}_screen_{index:02d}.txt"
        text_path.write_text(text, encoding="utf-8")
        paths.append(text_path)
    return paths


def textfile_arg(root: Path, text_file: Path) -> str:
    return ff_filter_escape(text_file.relative_to(root).as_posix())


def fade_alpha(start: float, end: float, fade: float = 0.35) -> str:
    return (
        f"if(lt(t,{start}+{fade}),(t-{start})/{fade},"
        f"if(gt(t,{end}-{fade}),({end}-t)/{fade},1))"
    )


def build_video_filter_v2(root: Path, scenario: dict, text_files: list[Path]) -> str:
    style = scenario.get("style", {})
    parse_resolution(style)
    overlay = float(style.get("dark_overlay", 0.48))
    text_color = str(style.get("text_color", "white"))
    accent = ffmpeg_color(str(style.get("accent_color", "#D39A3A")))
    duration = float(scenario["duration_seconds"])
    total_frames = max(1, int(duration * FPS))

    card_width = 990
    card_max_height = 620
    card_y = 170
    radius = 30
    rounded_alpha = rounded_alpha_expression(radius)

    # The shadow is intentionally subtle and approximate; the cover itself remains uncropped.
    filters = [
        (
            f"[0:v]split=2[bgsrc][cardsrc];"
            f"[bgsrc]scale={WIDTH}:{HEIGHT}:force_original_aspect_ratio=increase,"
            f"crop={WIDTH}:{HEIGHT},gblur=sigma=34,setsar=1,"
            f"zoompan=z='1+0.018*on/{total_frames}':"
            f"x='iw/2-(iw/zoom/2)':y='ih/2-(ih/zoom/2)':"
            f"d=1:s={WIDTH}x{HEIGHT}:fps={FPS},"
            f"drawbox=x=0:y=0:w=iw:h=ih:color=black@{overlay}:t=fill,"
            f"drawbox=x=0:y=0:w=iw:h=ih:color={ff_filter_escape(accent)}@0.05:t=fill,"
            f"drawbox=x=43:y={card_y + 24}:w=994:h=610:color=black@0.28:t=fill[bg];"
            f"[cardsrc]scale={card_width}:{card_max_height}:force_original_aspect_ratio=decrease,"
            f"format=rgba,"
            f"geq=r='r(X,Y)':g='g(X,Y)':b='b(X,Y)':a='{rounded_alpha}'[cover];"
            f"[bg][cover]overlay=(W-w)/2:{card_y}:format=auto,"
            f"drawbox=x=45:y={card_y - 4}:w=990:h=4:color={ff_filter_escape(accent)}@0.82:t=fill,"
            f"setsar=1[v0]"
        )
    ]

    screens = scenario.get("screens", [])
    previous = "v0"

    hook_end = float(screens[-1]["start"]) if len(screens) > 1 else duration
    hook_file = text_files[0]
    filters.append(
        (
            f"[{previous}]drawtext=fontfile='{FONT_FILE}':"
            f"textfile='{textfile_arg(root, hook_file)}':"
            f"fontcolor={ff_filter_escape(text_color)}:fontsize=68:"
            f"line_spacing=16:x=(w-text_w)/2:y=840:"
            f"shadowcolor=black@0.45:shadowx=0:shadowy=3:"
            f"enable='between(t,0,{hook_end})':alpha='{fade_alpha(0, hook_end, 0.45)}'"
            f"[v_hook]"
        )
    )
    previous = "v_hook"

    for index, screen in enumerate(screens[1:-1], start=2):
        start = float(screen["start"])
        end = float(screen["end"])
        text_file = text_files[index - 1]
        bar_label = f"v_bar_{index}"
        text_label = f"v_sub_{index}"
        alpha = fade_alpha(start, end, 0.30)
        filters.append(
            (
                f"[{previous}]drawbox=x=95:y=1115:w=890:h=150:"
                f"color=black@0.24:t=fill:enable='between(t,{start},{end})'["
                f"{bar_label}]"
            )
        )
        filters.append(
            (
                f"[{bar_label}]drawbox=x=120:y=1115:w=6:h=150:"
                f"color={ff_filter_escape(accent)}@0.85:t=fill:enable='between(t,{start},{end})',"
                f"drawtext=fontfile='{FONT_FILE}':"
                f"textfile='{textfile_arg(root, text_file)}':"
                f"fontcolor={ff_filter_escape(text_color)}:fontsize=46:"
                f"line_spacing=10:x=155:y=1152:"
                f"shadowcolor=black@0.28:shadowx=0:shadowy=2:"
                f"enable='between(t,{start},{end})':alpha='{alpha}'[{text_label}]"
            )
        )
        previous = text_label

    cta_start = float(screens[-1]["start"])
    cta_end = float(screens[-1]["end"])
    cta_file = text_files[-1]
    filters.append(
        (
            f"[{previous}]drawbox=x=110:y=1090:w=860:h=230:color=black@0.20:t=fill:"
            f"enable='between(t,{cta_start},{cta_end})',"
            f"drawbox=x=190:y=1325:w=700:h=4:color={ff_filter_escape(accent)}@0.92:t=fill:"
            f"enable='between(t,{cta_start},{cta_end})',"
            f"drawtext=fontfile='{FONT_FILE}':"
            f"textfile='{textfile_arg(root, cta_file)}':"
            f"fontcolor={ff_filter_escape(text_color)}:fontsize=58:"
            f"line_spacing=14:x=(w-text_w)/2:y=1145:"
            f"shadowcolor=black@0.38:shadowx=0:shadowy=3:"
            f"enable='between(t,{cta_start},{cta_end})':alpha='{fade_alpha(cta_start, cta_end, 0.40)}'"
            f"[v_cta]"
        )
    )
    previous = "v_cta"

    filters.append(
        (
            f"[{previous}]drawtext=fontfile='{FONT_FILE}':text='drycured.com':"
            f"fontcolor={ff_filter_escape(accent)}:fontsize=34:x=78:y=1710:"
            f"shadowcolor=black@0.25:shadowx=0:shadowy=2,"
            f"drawtext=fontfile='{FONT_FILE}':text='Digitalna pušnica znanja':"
            f"fontcolor=white@0.82:fontsize=28:x=78:y=1760:"
            f"shadowcolor=black@0.25:shadowx=0:shadowy=2,"
            f"drawbox=x=78:y=1690:w=924:h=1:color=white@0.18:t=fill,"
            f"format=yuv420p[vout]"
        )
    )
    return ";".join(filters)


def build_video_filter_legacy(root: Path, scenario: dict, text_files: list[Path]) -> str:
    style = scenario.get("style", {})
    parse_resolution(style)
    overlay = float(style.get("dark_overlay", 0.35))
    text_color = str(style.get("text_color", "white"))
    accent = ffmpeg_color(str(style.get("accent_color", "#D39A3A")))

    card_width = 1020
    card_max_height = 790
    card_y = 230
    text_center_y = 1055
    rounded_alpha = rounded_alpha_expression(28)

    filters = [
        (
            f"[0:v]split=2[bgsrc][cardsrc];"
            f"[bgsrc]scale={WIDTH}:{HEIGHT}:force_original_aspect_ratio=increase,"
            f"crop={WIDTH}:{HEIGHT},gblur=sigma=30,setsar=1,"
            f"drawbox=x=0:y=0:w=iw:h=ih:color=black@{overlay}:t=fill[bg];"
            f"[cardsrc]scale={card_width}:{card_max_height}:force_original_aspect_ratio=decrease,"
            f"format=rgba,drawbox=x=0:y=0:w=iw:h=ih:color={ff_filter_escape(accent)}@0.80:t=4,"
            f"geq=r='r(X,Y)':g='g(X,Y)':b='b(X,Y)':a='{rounded_alpha}'[cover];"
            f"[bg][cover]overlay=(W-w)/2:{card_y}:format=auto,setsar=1[v0]"
        )
    ]

    previous = "v0"
    screens = scenario.get("screens", [])
    for index, screen in enumerate(screens, start=1):
        start = float(screen["start"])
        end = float(screen["end"])
        box_color = "black@0.28"
        font_size = 66 if index == 1 else 56
        if index == len(screens):
            font_size = 62
        out_label = f"v{index}"
        draw_main = (
            f"[{previous}]drawtext=fontfile='{FONT_FILE}':"
            f"textfile='{textfile_arg(root, text_files[index - 1])}':"
            f"fontcolor={ff_filter_escape(text_color)}:fontsize={font_size}:"
            f"line_spacing=18:x=(w-text_w)/2:y={text_center_y}-(text_h/2):"
            f"box=1:boxcolor={box_color}:boxborderw=28:"
            f"enable='between(t,{start},{end})':alpha='{fade_alpha(start, end)}'"
        )
        if index == len(screens):
            draw_main += (
                f",drawbox=x=190:y=1290:w=700:h=4:color={ff_filter_escape(accent)}@0.90:t=fill:"
                f"enable='between(t,{start},{end})'"
            )
        filters.append(f"{draw_main}[{out_label}]")
        previous = out_label

    filters.append(f"[{previous}]format=yuv420p[vout]")
    return ";".join(filters)


def build_video_filter(root: Path, scenario: dict, text_files: list[Path]) -> str:
    if str(scenario.get("template_version", "")).lower() == "v2":
        return build_video_filter_v2(root, scenario, text_files)
    return build_video_filter_legacy(root, scenario, text_files)


def build_audio_filter(has_voice: bool, has_music: bool, duration: float) -> str | None:
    if has_voice and has_music:
        return (
            f"[1:a]atrim=0:{duration},asetpts=PTS-STARTPTS,dynaudnorm=f=150:g=9,volume=1.0[voice];"
            f"[2:a]atrim=0:{duration},asetpts=PTS-STARTPTS,volume=0.12[music];"
            "[voice][music]amix=inputs=2:duration=first:dropout_transition=2,"
            "aformat=sample_fmts=fltp:sample_rates=44100:channel_layouts=stereo[aout]"
        )
    if has_voice:
        return (
            f"[1:a]atrim=0:{duration},asetpts=PTS-STARTPTS,dynaudnorm=f=150:g=9,"
            "aformat=sample_fmts=fltp:sample_rates=44100:channel_layouts=stereo[aout]"
        )
    if has_music:
        return (
            f"[1:a]atrim=0:{duration},asetpts=PTS-STARTPTS,volume=0.22,"
            "aformat=sample_fmts=fltp:sample_rates=44100:channel_layouts=stereo[aout]"
        )
    return None


def scenario_output_path(root: Path, scenario: dict) -> Path:
    output_name = scenario.get("output")
    if not output_name:
        output_name = f"{scenario['id']}_{scenario['slug']}.mp4"
    return root / "01_VIDEO_SHORTS" / "output" / "videos" / output_name


def generate(script_path: Path) -> Path:
    root = project_root()
    ensure_structure(root)
    ffmpeg = require_ffmpeg()
    scenario = load_scenario(script_path)

    duration = float(scenario["duration_seconds"])
    image_path = root / "01_VIDEO_SHORTS" / "input" / "images" / scenario["image"]
    audio_name = scenario.get("audio")
    music_name = scenario.get("music")
    audio_path = root / "01_VIDEO_SHORTS" / "input" / "audio" / audio_name if audio_name else None
    music_path = root / "01_VIDEO_SHORTS" / "input" / "music" / music_name if music_name else None
    output_path = scenario_output_path(root, scenario)
    log_path = root / "01_VIDEO_SHORTS" / "logs" / f"{scenario['id']}_{datetime.now():%Y%m%d_%H%M%S}.log"

    if not image_path.exists():
        raise FileNotFoundError(f"Slika nije pronađena: {image_path}")

    has_voice = bool(audio_path and audio_path.exists())
    has_music = bool(music_path and music_path.exists())
    if audio_name and not has_voice:
        print(f"Upozorenje: podcast audio nije pronađen, nastavljam bez govora: {audio_path}")
    if music_name and not has_music:
        print(f"Upozorenje: glazba nije pronađena, nastavljam bez glazbe: {music_path}")

    text_files = write_text_files(root, scenario)
    video_filter = build_video_filter(root, scenario, text_files)
    audio_filter = build_audio_filter(has_voice, has_music, duration)
    filter_complex = video_filter if not audio_filter else f"{video_filter};{audio_filter}"

    cmd = [
        ffmpeg,
        "-y",
        "-hide_banner",
        "-loop",
        "1",
        "-framerate",
        str(FPS),
        "-t",
        str(duration),
        "-i",
        str(image_path),
    ]

    if has_voice:
        cmd.extend(["-ss", str(scenario.get("audio_start", "00:00:00"))])
        if scenario.get("audio_duration"):
            cmd.extend(["-t", str(scenario["audio_duration"])])
        cmd.extend(["-i", str(audio_path)])

    if has_music:
        cmd.extend(["-stream_loop", "-1", "-i", str(music_path)])

    cmd.extend(["-filter_complex", filter_complex, "-map", "[vout]"])
    if audio_filter:
        cmd.extend(["-map", "[aout]", "-c:a", "aac", "-b:a", "192k", "-shortest"])
    else:
        cmd.append("-an")

    cmd.extend(
        [
            "-r",
            str(FPS),
            "-c:v",
            "libx264",
            "-pix_fmt",
            "yuv420p",
            "-movflags",
            "+faststart",
            "-t",
            str(duration),
            str(output_path),
        ]
    )

    print(f"Renderiram: {output_path}")
    completed = subprocess.run(
        cmd,
        cwd=str(root),
        text=True,
        stdout=subprocess.PIPE,
        stderr=subprocess.PIPE,
        encoding="utf-8",
        errors="replace",
    )
    log_path.write_text(
        "\n".join(
            [
                "FFMPEG_COMMAND:",
                subprocess.list2cmdline(cmd),
                "",
                "STDOUT:",
                completed.stdout,
                "",
                "STDERR:",
                completed.stderr,
            ]
        ),
        encoding="utf-8",
    )
    if completed.returncode != 0:
        raise RuntimeError(f"FFmpeg render nije uspio. Log: {log_path}")

    print(f"Gotovo: {output_path}")
    print(f"Log: {log_path}")
    return output_path


def main() -> int:
    root = project_root()
    parser = argparse.ArgumentParser(description="Drycured Short Video Factory")
    parser.add_argument(
        "--script",
        default=str(root / "01_VIDEO_SHORTS" / "input" / "scripts" / "VID-001.json"),
        help="Putanja do JSON scenarija.",
    )
    args = parser.parse_args()

    try:
        generate(Path(args.script).resolve())
        return 0
    except Exception as exc:
        try:
            ensure_structure(root)
            log_path = root / "01_VIDEO_SHORTS" / "logs" / f"preflight_error_{datetime.now():%Y%m%d_%H%M%S}.log"
            log_path.write_text(
                "\n".join(
                    [
                        "PRE_RENDER_ERROR:",
                        str(exc),
                        "",
                        "SCRIPT:",
                        str(Path(args.script).resolve()),
                    ]
                ),
                encoding="utf-8",
            )
            print(f"Pre-render log: {log_path}", file=sys.stderr)
        except Exception:
            pass
        print(str(exc), file=sys.stderr)
        return 1


if __name__ == "__main__":
    raise SystemExit(main())

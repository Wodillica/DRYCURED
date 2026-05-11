@echo off
chcp 65001 >nul
setlocal

cd /d "%~dp0"
python "tools\generate_scene_video.py" --id VID-001

if errorlevel 1 (
  echo.
  echo Generiranje scene videa nije uspjelo. Provjeri 01_VIDEO_SHORTS\logs\VID-001_scene_video_debug.log.
  exit /b 1
)

echo.
echo Scene video VID-001 je generiran.
exit /b 0

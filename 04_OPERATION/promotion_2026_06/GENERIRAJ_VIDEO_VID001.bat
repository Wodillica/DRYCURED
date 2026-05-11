@echo off

set "FONTCONFIG_FILE=%~dp0tools\fontconfig\fonts.conf"
set "FONTCONFIG_PATH=%~dp0tools\fontconfig"
chcp 65001 >nul
setlocal

cd /d "%~dp0"
python "tools\generate_short_video.py" --script "01_VIDEO_SHORTS\input\scripts\VID-001.json"

if errorlevel 1 (
  echo.
  echo Generiranje nije uspjelo. Provjeri poruku iznad i logove u 01_VIDEO_SHORTS\logs.
  exit /b 1
)

echo.
echo Pilot video je generiran u 01_VIDEO_SHORTS\output\videos.
exit /b 0


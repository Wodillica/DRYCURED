@echo off
chcp 65001 >nul
setlocal

cd /d "%~dp0"

for %%F in ("01_VIDEO_SHORTS\input\scripts\VID-*.json") do (
  echo.
  echo Generiram %%~nxF
  python "tools\generate_short_video.py" --script "%%~fF"
  if errorlevel 1 (
    echo.
    echo Greška kod scenarija %%~nxF. Prekidam paket.
    exit /b 1
  )
)

echo.
echo Svi pilot videozapisi su generirani.
exit /b 0

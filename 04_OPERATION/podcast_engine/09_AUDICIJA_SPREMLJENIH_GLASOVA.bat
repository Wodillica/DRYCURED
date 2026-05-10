@echo off
chcp 65001 > nul
cd /d "%~dp0"

if not exist ".venv" (
  call 01_POSTAVI_OKRUZENJE.bat
)

call ".venv\Scripts\activate.bat"
python scripts\audition_saved_voices.py

echo.
echo Audicija spremljenih glasova je u mapi:
echo output\audicija_spremljenih_glasova
echo.
start "" "output\audicija_spremljenih_glasova"
pause

@echo off
chcp 65001 > nul
cd /d "%~dp0"

if not exist ".venv" (
  call 01_POSTAVI_OKRUZENJE.bat
)

call ".venv\Scripts\activate.bat"
python scripts\audition_voices.py

echo.
echo Audicija glasova je u mapi:
echo output\audicija_glasova
echo.
start "" "output\audicija_glasova"
pause

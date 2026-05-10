@echo off
chcp 65001 > nul
cd /d "%~dp0"

if not exist ".venv" (
  call 01_POSTAVI_OKRUZENJE.bat
)

call ".venv\Scripts\activate.bat"
python scripts\podcast_engine.py --list-voices

echo.
echo Ako je popis glasova prikazan, API key radi.
echo Sljedece pokreni: 03_GENERIRAJ_TEST_EP01.bat
pause

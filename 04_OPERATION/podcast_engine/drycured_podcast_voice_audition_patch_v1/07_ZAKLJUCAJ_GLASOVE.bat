@echo off
chcp 65001 > nul
cd /d "%~dp0"

echo ============================================
echo Drycured Podcast Engine - zakljucavanje glasova
echo ============================================
echo.
echo Otvori output\audicija_glasova\AUDICIJA_GLASOVA_INDEX.csv
echo Odaberi voice_id za VODITELJA i MAJSTORA.
echo.

set /p HOST_ID=Zalijepi voice_id za VODITELJA: 
set /p MASTER_ID=Zalijepi voice_id za MAJSTORA: 

call ".venv\Scripts\activate.bat"
python scripts\set_voices_config.py "%HOST_ID%" "%MASTER_ID%"

pause

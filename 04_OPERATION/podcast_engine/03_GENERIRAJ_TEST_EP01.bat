@echo off
chcp 65001 > nul
cd /d "%~dp0"

call ".venv\Scripts\activate.bat"
python scripts\podcast_engine.py --episode EP01

echo.
echo Testna epizoda je u output\final\
pause

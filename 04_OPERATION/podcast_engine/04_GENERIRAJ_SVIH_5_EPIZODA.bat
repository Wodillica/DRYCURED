@echo off
chcp 65001 > nul
cd /d "%~dp0"

call ".venv\Scripts\activate.bat"
python scripts\podcast_engine.py --render-all

echo.
echo Sve epizode su u output\final\
pause

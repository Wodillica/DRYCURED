@echo off
chcp 65001 > nul
cd /d "%~dp0"

echo Brisem stare EP01 segmente da se test napravi s novim glasovima...
if exist "output\segments\EP01" rmdir /s /q "output\segments\EP01"
if exist "output\final\EP01_osnove-dobrog-suhomesnatog-proizvoda.mp3" del /q "output\final\EP01_osnove-dobrog-suhomesnatog-proizvoda.mp3"
if exist "output\final\EP01_osnove-dobrog-suhomesnatog-proizvoda.concat.txt" del /q "output\final\EP01_osnove-dobrog-suhomesnatog-proizvoda.concat.txt"

call ".venv\Scripts\activate.bat"
python scripts\podcast_engine.py --episode EP01

echo.
echo Testna epizoda s novim glasovima je u output\final\
start "" "output\final"
pause

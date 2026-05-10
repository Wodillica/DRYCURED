@echo off
chcp 65001 > nul
cd /d "%~dp0"

echo ============================================
echo Drycured Podcast Engine v1 - postavljanje
echo ============================================

if not exist ".env" (
  copy ".env.example" ".env" > nul
  echo Kreirana je .env datoteka.
  echo Otvaram .env - zalijepi ELEVENLABS_API_KEY i spremi.
  notepad ".env"
  echo Nakon spremanja pokreni 02_PROVJERI_GLASOVE.bat
  pause
  exit /b
)

if not exist ".venv" (
  py -m venv .venv
)

call ".venv\Scripts\activate.bat"
python -m pip install --upgrade pip
pip install -r requirements.txt

echo.
echo OK. Okruzenje je spremno.
echo Sljedece pokreni: 02_PROVJERI_GLASOVE.bat
pause

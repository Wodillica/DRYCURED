@echo off
cd /d "%~dp0"
if not exist "output\final" mkdir "output\final"
explorer "output\final"

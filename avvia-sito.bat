@echo off
rem Avvia il sito Rift Academy con Docker e lo apre nel browser.
rem Per fermarlo: chiudi questa finestra e lancia "docker compose down" in questa cartella.

cd /d "%~dp0"

docker info >nul 2>&1
if errorlevel 1 (
    echo Docker non e' avviato. Apri Docker Desktop, aspetta che sia pronto e riprova.
    pause
    exit /b 1
)

echo Avvio dei container in corso...
docker compose up -d --build
if errorlevel 1 (
    echo Errore durante l'avvio dei container.
    pause
    exit /b 1
)

echo Attendo che il database sia pronto...
timeout /t 8 /nobreak >nul

start "" http://localhost:8081
echo Sito avviato su http://localhost:8081
pause

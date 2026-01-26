@echo off
echo ====================================
echo GESTIONALE FREELANCE - SETUP RAPIDO
echo ====================================
echo.

REM Controlla se composer è installato
where composer >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERRORE] Composer non trovato. Installalo prima di continuare.
    pause
    exit /b 1
)

REM Controlla se npm è installato
where npm >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERRORE] Node.js/npm non trovato. Installalo prima di continuare.
    pause
    exit /b 1
)

echo [1/8] Installazione dipendenze PHP...
call composer install
if %ERRORLEVEL% NEQ 0 (
    echo [ERRORE] Installazione composer fallita!
    pause
    exit /b 1
)

echo.
echo [2/8] Copia file .env...
if not exist .env (
    copy .env.example .env
    echo File .env creato. RICORDATI DI CONFIGURARE IL DATABASE!
) else (
    echo File .env già esistente, non sovrascritto.
)

echo.
echo [3/8] Generazione chiave applicazione...
call php artisan key:generate

echo.
echo [4/8] Installazione dipendenze Node.js...
call npm install
if %ERRORLEVEL% NEQ 0 (
    echo [ERRORE] Installazione npm fallita!
    pause
    exit /b 1
)

echo.
echo [5/8] Compilazione asset...
call npm run build
if %ERRORLEVEL% NEQ 0 (
    echo [ERRORE] Compilazione asset fallita!
    pause
    exit /b 1
)

echo.
echo [6/8] Controlla configurazione database...
echo IMPORTANTE: Verifica che il file .env abbia le credenziali corrette!
echo Premi un tasto dopo aver configurato il database nel file .env...
pause >nul

echo.
echo [7/8] Esecuzione migration database...
call php artisan migrate
if %ERRORLEVEL% NEQ 0 (
    echo [ERRORE] Migration fallita! Controlla la configurazione database nel file .env
    pause
    exit /b 1
)

echo.
echo [8/8] Vuoi caricare i dati di esempio? (s/n)
set /p SEED="Risposta: "
if /i "%SEED%"=="s" (
    echo Caricamento dati di esempio...
    call php artisan db:seed
    echo.
    echo Dati di esempio caricati!
    echo Credenziali di accesso:
    echo - Email: test@example.com
    echo - Password: password
)

echo.
echo ====================================
echo SETUP COMPLETATO CON SUCCESSO!
echo ====================================
echo.
echo Per avviare il server esegui:
echo   php artisan serve
echo.
echo Poi apri il browser su:
echo   http://localhost:8000
echo.
echo Buon lavoro!
echo ====================================
pause

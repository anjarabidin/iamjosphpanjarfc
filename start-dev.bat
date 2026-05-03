@echo off
cd /d "%~dp0"

REM Add Herd paths
set PATH=C:\Users\%USERNAME%\.config\herd\bin\php83;C:\Users\%USERNAME%\.config\herd\bin;%PATH%
set PHP_CLI_SERVER_WORKERS=

echo.
echo ========================================
echo  IAMJOS Laravel Application Startup
echo ========================================
echo.
echo Starting Laravel development server...
echo Server will run on: http://127.0.0.1:3000
echo.
echo Press Ctrl+C to stop the server.
echo.

REM Run the dev server
php artisan serve --host=127.0.0.1 --port=3000 --no-reload

@echo off
chcp 65001 >nul
cd /d "%~dp0"

if not exist vendor\autoload.php (
    echo Dependencias nao instaladas. Execute install.bat primeiro.
    pause
    exit /b 1
)

echo Sistema rodando em http://localhost:8000/login
echo Para parar, feche esta janela ou pressione Ctrl+C.
echo.
php artisan serve --port=8000

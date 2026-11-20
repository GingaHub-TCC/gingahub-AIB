@echo off
chcp 65001 >nul
setlocal
cd /d "%~dp0"

echo === GingaHub: preparando o ambiente de desenvolvimento ===
echo.

where php >nul 2>nul
if errorlevel 1 (
    echo [ERRO] PHP nao encontrado no PATH.
    echo Instale o XAMPP e adicione a pasta do PHP ao PATH, por exemplo C:\xampp\php
    goto fim_erro
)

php -r "exit(PHP_VERSION_ID >= 80200 ? 0 : 1);"
if errorlevel 1 (
    echo [ERRO] E necessario PHP 8.2 ou superior.
    goto fim_erro
)

where composer >nul 2>nul
if errorlevel 1 (
    echo [ERRO] Composer nao encontrado. Instale em https://getcomposer.org
    goto fim_erro
)

echo [1/3] Instalando dependencias do Composer...
call composer install --no-interaction
if errorlevel 1 (
    echo [ERRO] composer install falhou.
    goto fim_erro
)

echo.
echo [2/3] Preparando .env, chave e banco...
php scripts\preparar_ambiente.php
set RESULTADO=%errorlevel%
if "%RESULTADO%"=="1" goto fim_erro

echo.
echo [3/3] Criando tabelas...
if "%RESULTADO%"=="0" (
    php artisan migrate --seed
) else (
    echo Banco ja tem tabelas: aplicando apenas migrations pendentes, sem apagar dados.
    php artisan migrate
)
if errorlevel 1 (
    echo [ERRO] Falha ao rodar as migrations.
    goto fim_erro
)

echo.
echo === Pronto! ===
echo Para iniciar o sistema, execute start.bat
echo Depois abra http://localhost:8000/login
echo Contas de teste e senha: veja a secao "Contas de teste" do README.md
echo.
pause
exit /b 0

:fim_erro
echo.
echo A preparacao parou por causa do erro acima.
pause
exit /b 1

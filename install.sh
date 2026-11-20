#!/usr/bin/env bash
# Prepara o ambiente do GingaHub (Linux/macOS). Não instala PHP, Composer nem MySQL.
set -u
cd "$(dirname "$0")"

echo "=== GingaHub: preparando o ambiente de desenvolvimento ==="
echo

if ! command -v php >/dev/null 2>&1; then
    echo "[ERRO] PHP nao encontrado. Instale o PHP 8.2 ou superior."
    exit 1
fi

if ! php -r 'exit(PHP_VERSION_ID >= 80200 ? 0 : 1);'; then
    echo "[ERRO] E necessario PHP 8.2 ou superior."
    exit 1
fi

if ! command -v composer >/dev/null 2>&1; then
    echo "[ERRO] Composer nao encontrado. Instale em https://getcomposer.org"
    exit 1
fi

echo "[1/3] Instalando dependencias do Composer..."
if ! composer install --no-interaction; then
    echo "[ERRO] composer install falhou."
    exit 1
fi

echo
echo "[2/3] Preparando .env, chave e banco..."
php scripts/preparar_ambiente.php
RESULTADO=$?
if [ "$RESULTADO" -eq 1 ]; then
    echo
    echo "A preparacao parou por causa do erro acima."
    exit 1
fi

echo
echo "[3/3] Criando tabelas..."
if [ "$RESULTADO" -eq 0 ]; then
    php artisan migrate --seed || { echo "[ERRO] Falha ao rodar as migrations."; exit 1; }
else
    echo "Banco ja tem tabelas: aplicando apenas migrations pendentes, sem apagar dados."
    php artisan migrate || { echo "[ERRO] Falha ao rodar as migrations."; exit 1; }
fi

echo
echo "=== Pronto! ==="
echo "Para iniciar o sistema, execute: ./start.sh"
echo "Depois abra http://localhost:8000/login"
echo "Contas de teste e senha: veja a secao 'Contas de teste' do README.md"

#!/usr/bin/env bash
# Sobe o GingaHub em http://localhost:8000/login
cd "$(dirname "$0")" || exit 1

if [ ! -f vendor/autoload.php ]; then
    echo "Dependencias nao instaladas. Execute ./install.sh primeiro."
    exit 1
fi

echo "Sistema rodando em http://localhost:8000/login"
echo "Para parar, pressione Ctrl+C."
echo
exec php artisan serve --port=8000

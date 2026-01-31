#!/bin/bash
set -e

cd /var/www

# Verificar se composer.json existe
if [ -f "composer.json" ]; then
    # Instalar dependências (sem dev em produção)
    if [ "$APP_ENV" = "production" ]; then
        composer install --no-dev --optimize-autoloader --no-interaction
    else
        composer install --no-interaction
    fi
fi

# Executar comando passado ou manter container rodando
exec "$@"

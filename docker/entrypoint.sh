#!/bin/sh

# Laravel necesita que la variable APP_KEY exista físicamente en el archivo para que key:generate la reemplace.
if [ ! -f .env ]; then
    echo "APP_KEY=" > .env
fi

# Verificamos si la variable APP_KEY está vacía en el entorno y procedemos a generarla si es necesario
if [ -z "$APP_KEY" ]; then
    echo "No se detectó APP_KEY. Generando una nueva clave criptográfica..."
    php artisan key:generate --no-interaction --force
fi

# Optimizamos la configuración y rutas de Laravel para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutamos el comando principal (Supervisord)
exec "$@"
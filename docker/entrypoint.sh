#!/bin/sh

echo "🚀 Iniciando Health Manager..."

# 1. Gestión de la APP_KEY
# Laravel necesita que la variable APP_KEY exista físicamente en el archivo para que key:generate la reemplace.
if [ ! -f .env ]; then
    echo "APP_KEY=" > .env
fi

# Verificamos si la variable APP_KEY está vacía en el entorno y procedemos a generarla si es necesario
if [ -z "$APP_KEY" ]; then
    echo "🔑 No se detectó APP_KEY. Generando una nueva clave criptográfica..."
    php artisan key:generate --no-interaction --force
fi

echo "📂 Configurando directorios de almacenamiento y permisos..."

# 1. Crear las carpetas críticas si no existen
mkdir -p /var/www/html/storage/app/livewire-tmp
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/logs

# 2. Forzar la creación del archivo de log para que no falle
touch /var/www/html/storage/logs/laravel.log

# 3. Asignar el propietario al usuario de Nginx/PHP (www-data)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 4. Dar permisos generosos (775) a las carpetas
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 5. Crear el enlace simbólico
php artisan storage:link --force

# 3. Optimizamos la configuración y rutas de Laravel para producción
echo "🔥 Cacheando configuración..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Ejecutamos el comando principal (Supervisord o php-fpm)
echo "✅ Todo listo. Arrancando proceso principal..."
exec "$@"
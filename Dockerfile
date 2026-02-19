# ==========================================
# Etapa 1: Compilación de Frontend (Vite)
# ==========================================
FROM node:22-alpine AS frontend

WORKDIR /app

# Copiar archivos de dependencias
COPY package.json package-lock.json* ./

# Instalar dependencias
RUN npm ci

# Copiar el resto del código y compilar assets
COPY . .
RUN npm run build

# ==========================================
# Etapa 2: Entorno de Producción y Backend
# ==========================================
FROM php:8.4-fpm-alpine

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Establecer variables de entorno por defecto (sin secretos ni interpolaciones)
ENV APP_NAME="Health Manager" \
    APP_ENV="production" \
    APP_URL="http://localhost" \
    APP_LOCALE="es" \
    DB_CONNECTION="pgsql" \
    DB_HOST="db" \
    DB_PORT="5432" \
    DB_DATABASE="health_manager" \
    DB_USERNAME="postgres" \
    MAIL_MAILER="log" \
    MAIL_HOST="127.0.0.1" \
    MAIL_PORT="2525" \
    MAIL_USERNAME="" \
    MAIL_FROM_ADDRESS="hello@example.com"

# Instalar dependencias del sistema, Nginx, Supervisor y librerías para extensiones PHP
RUN apk update && apk add --no-cache \
    nginx \
    supervisor \
    postgresql-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    curl-dev \
    libxml2-dev \
    zip \
    unzip

# Instalar y habilitar extensiones de PHP
RUN docker-php-ext-install \
    pdo_pgsql \
    mbstring \
    xml \
    bcmath \
    curl \
    zip \
    intl \
    opcache

# Configurar OPcache para optimizar el rendimiento en producción
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar los archivos del proyecto
COPY . .

# Copiar los assets compilados de la etapa de frontend
COPY --from=frontend /app/public/build ./public/build

# Instalar dependencias de PHP para producción
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Otorgar permisos al usuario www-data (usuario estándar de PHP-FPM y Nginx)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Copiar archivos de configuración de Nginx y Supervisor
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Configurar Nginx para que se ejecute bajo el usuario www-data
RUN sed -i 's/user nginx;/user www-data;/' /etc/nginx/nginx.conf

# Copiar el script de entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Exponer el puerto web
EXPOSE 80

# Definir el Entrypoint y el Comando por defecto
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
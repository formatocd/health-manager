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
# Etapa 2: Entorno de Producción y Backend (Debian)
# ==========================================
FROM php:8.4-fpm

WORKDIR /var/www/html

# Establecer variables de entorno
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

# 1. Instalar dependencias de Debian
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    curl \
    libcurl4-openssl-dev \
    libxml2-dev \
    zip \
    unzip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    file \
    media-types \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2. Configurar e instalar extensión GD
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# 3. Instalar resto de extensiones PHP
RUN docker-php-ext-install \
    pdo_pgsql \
    mbstring \
    xml \
    bcmath \
    curl \
    zip \
    intl \
    opcache \
    exif

# 4. Configurar OPcache
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini

# 5. Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 6. Copiar archivos del proyecto
COPY . .

# 7. Copiar assets del frontend
COPY --from=frontend /app/public/build ./public/build

# 8. Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# 9. Permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# 10. Configurar Nginx y Supervisor en Debian
# (Debian guarda la config de nginx en conf.d en vez de http.d y borramos la ruta por defecto para que no haya conflictos)
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
RUN rm -f /etc/nginx/sites-enabled/default

COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# 11. Copiar Entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
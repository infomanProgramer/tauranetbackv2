FROM php:8.1-apache

# Instalar dependencias del sistema
RUN apt-get update \
 && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    curl \
 && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pgsql \
        mbstring \
        exif \
        bcmath \
        zip \
        gd

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Cambiar DocumentRoot a /var/www/public
RUN sed -i 's|/var/www/html|/var/www/public|g' /etc/apache2/sites-available/000-default.conf

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Limpiar cache de Laravel (Render Free)
RUN php artisan config:clear || true
RUN php artisan cache:clear || true
RUN php artisan route:clear || true

# PHP config
COPY ./docker/php.ini /usr/local/etc/php/

# Permisos
RUN mkdir -p storage bootstrap/cache \
 && chmod -R 777 storage bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80

CMD ["apache2-foreground"]

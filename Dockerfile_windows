FROM php:7.2-apache

# Instalar dependencias del sistema necesarias para extensiones
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    git \
    curl

# Instalar extensiones de PHP
RUN docker-php-ext-configure gd \
        --with-freetype-dir=/usr/include/ \
        --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        pgsql \
        mbstring \
        exif \
        bcmath \
        zip \
        gd

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Cambiar DocumentRoot a /var/www/public
RUN sed -i 's|/var/www/html|/var/www/public|g' /etc/apache2/sites-available/000-default.conf

# Instalar Composer 1.8.6
RUN curl -sS https://getcomposer.org/download/1.8.6/composer.phar -o /usr/local/bin/composer && \
    chmod +x /usr/local/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-interaction --prefer-dist

COPY ./docker/php.ini /usr/local/etc/php/

RUN mkdir -p /var/www/bootstrap/cache \
    && chmod -R 777 /var/www/storage /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Exponer el puerto de PHP-FPM
EXPOSE 80

CMD ["apache2-foreground"]

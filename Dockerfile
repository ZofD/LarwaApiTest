FROM php:8.2-apache

# Instalacja zależności systemowych
RUN apt-get update && apt-get install -y \
    zip unzip curl git libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql zip gd

# Włączenie modułów Apache
RUN a2enmod rewrite

# Instalacja Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Kopiuj plik konfiguracyjny Apache
COPY ./docker/apache/default.conf /etc/apache2/sites-available/000-default.conf

# Ustaw katalog roboczy
WORKDIR /var/www

# Ustaw właściciela i prawa
RUN chown -R www-data:www-data /var/www
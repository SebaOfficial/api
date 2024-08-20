FROM php:8.3-apache

WORKDIR /var/www/html

COPY . .

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY conf.d/apache.conf /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    nodejs \
    npm \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

RUN composer install \
    --no-ansi \
    --no-dev \
    --no-plugins \
    --no-progress \
    --no-scripts \
    --classmap-authoritative \
    --no-interaction

RUN ADMIN_PASSWORD='qwe123' composer bootstrap --no-plugins

EXPOSE 80

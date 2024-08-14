FROM php:8.3-apache

WORKDIR /var/www/html

COPY . .

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY conf.d/apache.conf /etc/apache2/sites-available/000-default.conf

RUN a2enmod rewrite

RUN composer install \
    --no-ansi \
    --no-dev \
    --no-plugins \
    --no-progress \
    --no-scripts \
    --classmap-authoritative \
    --no-interaction

EXPOSE 80

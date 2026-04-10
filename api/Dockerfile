FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip \
    && pecl install redis \
    && docker-php-ext-enable redis

COPY --from=composer:2.9 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

USER root
RUN chmod +x docker-entrypoint.sh

RUN composer install --optimize-autoloader --no-interaction --prefer-dist

EXPOSE 8000

FROM php:8.2-fpm-alpine AS base

RUN apk add --no-cache \
    bash git curl zip unzip \
    icu-dev libzip-dev oniguruma-dev \
    freetype-dev libjpeg-turbo-dev libpng-dev \
    $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql intl zip gd pcntl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del --no-cache $PHPIZE_DEPS

WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader

COPY . .

RUN php artisan config:clear || true

CMD ["php-fpm"]


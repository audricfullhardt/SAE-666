# syntax=docker/dockerfile:1

FROM php:8.4-fpm-alpine AS backend_base

RUN apk add --no-cache git unzip icu-libs libzip oniguruma \
 && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev libzip-dev oniguruma-dev \
 && docker-php-ext-install -j"$(nproc)" pdo_mysql intl opcache zip mbstring \
 && apk del .build-deps

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/backend/php.ini /usr/local/etc/php/conf.d/zz-dinoquest.ini
COPY docker/backend/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

WORKDIR /var/www/backend

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]

# Dev : code monté en volume (voir docker-compose.override.yml).
FROM backend_base AS backend_dev
ENV APP_ENV=dev

# Prod : code et dépendances figés dans l'image.
FROM backend_base AS backend_prod
ENV APP_ENV=prod
COPY backend/ /var/www/backend/
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress \
 && mkdir -p var && chown -R www-data:www-data var

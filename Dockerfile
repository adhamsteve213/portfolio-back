FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

FROM node:22-alpine AS frontend

WORKDIR /app

COPY package.json ./
RUN npm install

COPY resources ./resources
COPY vite.config.* ./
COPY public ./public

RUN npm run build

FROM php:8.3-cli-alpine

WORKDIR /var/www/html

RUN apk add --no-cache bash curl git icu-dev libzip-dev oniguruma-dev $PHPIZE_DEPS \
    && docker-php-ext-install pdo pdo_mysql intl mbstring zip opcache \
    && apk del $PHPIZE_DEPS

COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build
COPY . .

RUN mkdir -p storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && php artisan storage:link || true \
    && chmod -R ug+rwX storage bootstrap/cache

EXPOSE 8000

CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=8000"]
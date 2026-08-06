FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git unzip zip curl \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libzip-dev libpq-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo pdo_mysql pdo_pgsql gd zip opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# copy composer dulu
COPY composer.json composer.lock ./

# install vendor TANPA scripts dulu
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts

# baru copy semua source
COPY . .

# generate autoload setelah source ada
RUN composer dump-autoload --optimize

# build frontend
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
 && apt-get install -y nodejs \
 && npm install \
 && npm run build \
 && rm -rf node_modules

# permission hanya yg perlu
RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

COPY docker/php.ini /usr/local/etc/php/conf.d/uploads.ini

EXPOSE 8080

CMD php artisan optimize || true; \
    php artisan storage:link || true; \ 
    php artisan migrate --path=database/migrations/migrasi_2_event --force \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
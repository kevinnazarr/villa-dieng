FROM php:8.4-cli

# pdo_pgsql for PostgreSQL, pcntl for queue workers, phpredis to match
# Laravel's default REDIS_CLIENT=phpredis.
RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev unzip git \
    && docker-php-ext-install pdo_pgsql pcntl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-scripts

COPY . .
RUN composer dump-autoload --optimize

EXPOSE 8000

# --no-reload: artisan serve strips Docker env vars from the php -S worker
# unless this flag is passed (only $passthroughVariables survive otherwise).
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000", "--no-reload"]

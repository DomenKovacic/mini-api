FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

CMD ["bash", "-lc", "if [ ! -f .env ]; then cp .env.example .env; fi && composer install && touch database/database.sqlite && php artisan key:generate --force && php artisan migrate --seed --force && php artisan serve --host=0.0.0.0 --port=8000"]
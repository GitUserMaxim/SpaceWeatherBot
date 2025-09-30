FROM php:8.2-cli

RUN docker-php-ext-install mbstring

WORKDIR /app
COPY . /app

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader

CMD ["php", "index.php"]

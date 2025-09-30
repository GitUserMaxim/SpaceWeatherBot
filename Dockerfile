FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpq-dev \
    libonig-dev \
    curl \
    unzip \
    git \
    gcc \
    make \
    autoconf \
    libc-dev \
    pkg-config \
    && docker-php-ext-install zip pdo pdo_pgsql mbstring \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader

EXPOSE 10000

CMD ["php", "-S", "0.0.0.0:10000", "index.php"]

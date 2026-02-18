FROM php:fpm

# Install system dependencies + GD (with WebP) + Composer
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    git unzip libzip-dev \
    libpng-dev libjpeg-dev libwebp-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd pdo pdo_mysql \
    && curl -sS https://getcomposer.org/installer -o composer-setup.php \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm composer-setup.php \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER=1

CMD ["sh", "-lc", "[ -f vendor/autoload.php ] || composer install --no-interaction --no-progress; exec php-fpm"]

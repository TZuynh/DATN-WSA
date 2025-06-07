FROM php:8.2-apache

# Cài đặt extension
RUN apt-get update && apt-get install -y \
    git zip unzip libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

RUN a2enmod rewrite

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy source code
COPY . .

# ✅ Tạo .env trước khi cài composer
RUN if [ -f .env.example ]; then cp .env.example .env; fi

# ✅ Cài đặt composer dependencies
RUN composer install --no-interaction --no-dev --optimize-autoloader

# ✅ Phân quyền cho storage
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 80

CMD ["/bin/bash", "-c", "\
  if [ ! -f .env ] && [ -f .env.example ]; then cp .env.example .env; fi && \
  if ! grep -q 'APP_KEY=' .env || grep -q 'APP_KEY=$' .env; then php artisan key:generate; fi && \
  apache2-foreground \
"]

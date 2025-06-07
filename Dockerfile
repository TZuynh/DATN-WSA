FROM php:8.2-apache

# Cài đặt extension cần thiết
RUN apt-get update && apt-get install -y \
    git zip unzip libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Enable mod_rewrite cho Apache
RUN a2enmod rewrite

# Cài composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy mã nguồn vào container
COPY . .

# Cài đặt PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Chỉnh quyền cho storage và cache
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose port 80
EXPOSE 80

# Lệnh start: Tự động tạo .env nếu chưa có, tạo APP_KEY nếu chưa có, rồi start Apache
CMD ["/bin/bash", "-c", "\
  if [ ! -f .env ] && [ -f .env.example ]; then cp .env.example .env; fi && \
  if ! grep -q 'APP_KEY=' .env || grep -q 'APP_KEY=$' .env; then php artisan key:generate; fi && \
  apache2-foreground \
"]

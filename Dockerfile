FROM php:8.2-apache

# Cài các PHP extension thường dùng trong Laravel
RUN apt-get update && apt-get install -y \
    git unzip zip curl libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Bật mod_rewrite để hỗ trợ Laravel route đẹp
RUN a2enmod rewrite

# Cài đặt Composer (copy từ image composer chính thức)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Đặt thư mục làm việc
WORKDIR /var/www/html

# Copy toàn bộ mã nguồn Laravel
COPY . .

# Copy file .env nếu chưa có
RUN if [ ! -f .env ] && [ -f .env.example ]; then cp .env.example .env; fi

# Cài composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Tạo các thư mục cần thiết và gán quyền
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# EXPOSE cổng mặc định của Apache
EXPOSE 80

# Lệnh mặc định: tạo APP_KEY nếu chưa có rồi start Apache
CMD ["/bin/bash", "-c", "\
  if ! grep -q '^APP_KEY=' .env || grep -q 'APP_KEY=$' .env; then php artisan key:generate; fi && \
  apache2-foreground \
"]

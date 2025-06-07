FROM php:8.2-fpm

# Cài đặt các dependencies
RUN apt-get update && apt-get install -y \
  git \
  curl \
  libpng-dev \
  libonig-dev \
  libxml2-dev \
  zip \
  unzip

# Cài đặt PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Tạo thư mục làm việc
WORKDIR /var/www

# Copy composer files
COPY composer.json composer.lock ./

# Cài đặt dependencies
RUN composer install --no-interaction --no-dev --optimize-autoloader --no-scripts

# Copy toàn bộ source code
COPY . .

# Cấp quyền cho storage và cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expose port 9000
EXPOSE 9000

CMD ["php-fpm"]

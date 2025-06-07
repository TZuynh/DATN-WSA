FROM php:8.2-apache

# Cài đặt các dependencies cần thiết
RUN apt-get update && apt-get install -y \
  git \
  zip \
  unzip \
  libpng-dev \
  libjpeg-dev \
  libfreetype6-dev \
  libonig-dev \
  libxml2-dev \
  curl \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/*

# Bật mod rewrite cho Apache
RUN a2enmod rewrite

# Cài đặt Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Thiết lập thư mục làm việc
WORKDIR /var/www/html

# Copy composer files trước
COPY composer.json composer.lock ./

# Cài đặt dependencies
RUN composer install --no-interaction --no-dev --optimize-autoloader --no-scripts

# Copy toàn bộ source code
COPY . .

# Tạo file .env nếu chưa tồn tại
RUN if [ -f .env.example ]; then cp .env.example .env; fi

# Phân quyền cho storage và cache
RUN chown -R www-data:www-data storage bootstrap/cache \
  && chmod -R 775 storage bootstrap/cache

EXPOSE 80

CMD ["/bin/bash", "-c", "\
  if [ ! -f .env ] && [ -f .env.example ]; then cp .env.example .env; fi && \
  if ! grep -q 'APP_KEY=' .env || grep -q 'APP_KEY=$' .env; then php artisan key:generate; fi && \
  apache2-foreground \
  "]

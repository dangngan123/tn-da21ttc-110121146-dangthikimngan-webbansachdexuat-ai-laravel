FROM php:8.2-fpm

# Cài PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip unzip git curl \
    nginx supervisor \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd zip

# Cài Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Thiết lập thư mục làm việc
WORKDIR /var/www

# Copy source Laravel MVC + Livewire
COPY . /var/www

# Cài đặt composer
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Phân quyền cho Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Copy file cấu hình Nginx
COPY ./nginx.conf /etc/nginx/nginx.conf

# Supervisor cấu hình để chạy cả php-fpm và nginx
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord"]

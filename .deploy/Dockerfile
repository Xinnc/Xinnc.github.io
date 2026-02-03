FROM php:8.4-fpm

# Устанавливаем пакеты
RUN apt-get update && apt-get install -y \
    nginx \
    libpq-dev \
    git \
    curl \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql \
    && apt-get clean

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Рабочая директория
WORKDIR /var/www

# Копируем проект
COPY . .

# Устанавливаем зависимости
RUN composer install --no-dev --optimize-autoloader

# Права
RUN chown -R www-data:www-data storage bootstrap/cache

# Удаляем дефолтный nginx сайт
RUN rm -f /etc/nginx/sites-enabled/default

# Кладём свой конфиг как основной сайт
COPY .deploy/nginx.conf /etc/nginx/sites-enabled/default

CMD php artisan migrate --force --seed && service nginx start && php-fpm


EXPOSE 80

CMD service nginx start && php-fpm

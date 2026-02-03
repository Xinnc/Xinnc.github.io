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

# Удаляем дефолтный nginx конфиг и кладём свой
RUN rm -f /etc/nginx/conf.d/default.conf
COPY .deploy/nginx.conf /etc/nginx/conf.d/default.conf


EXPOSE 80

CMD service nginx start && php-fpm

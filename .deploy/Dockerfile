FROM php:8.4-fpm

# Устанавливаем пакеты + postgresql-client для pg_isready
RUN apt-get update \
    && apt-get install -y \
        nginx \
        libpq-dev \
        git \
        curl \
        zip \
        unzip \
        postgresql-client \
    && docker-php-ext-install pdo pdo_pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

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

# Копируем nginx.conf
COPY .deploy/nginx.conf /etc/nginx/sites-enabled/default

# Копируем и делаем исполняемым entrypoint
COPY .deploy/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# ENTRYPOINT запускает миграции + сервисы
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

EXPOSE 80

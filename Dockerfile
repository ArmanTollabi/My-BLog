# ---------- مرحله ۱: ساخت فرانت‌اند (Vite / Tailwind) ----------
FROM node:22-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# ---------- مرحله ۲: اپ نهایی با PHP 8.4 ----------
FROM php:8.4-apache

RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libzip-dev libicu-dev libpng-dev libjpeg-dev libfreetype6-dev libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql bcmath intl zip gd mbstring opcache pcntl \
    && rm -rf /var/lib/apt/lists/*

# فقط یک MPM (prefork) فعال بماند؛ فایل‌های بقیه مستقیم پاک می‌شوند
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load /etc/apache2/mods-enabled/mpm_*.conf \
    && ln -s ../mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load \
    && ln -s ../mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf \
    && a2enmod rewrite

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# پورت را از متغیر PORT (که Railway می‌دهد) بخوان؛ فقط یک بار در زمان Build ویرایش می‌شود
ENV PORT=8080
RUN sed -ri 's/Listen 80$/Listen ${PORT}/' /etc/apache2/ports.conf \
    && sed -ri 's/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/' /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

COPY . .
COPY --from=assets /app/public/build ./public/build
RUN composer dump-autoload --optimize --no-dev \
    && chown -R www-data:www-data storage bootstrap/cache

# خط اول فقط برای عیب‌یابی است: MPM های فعال را در لاگ نشان می‌دهد
CMD ["sh", "-c", "ls /etc/apache2/mods-enabled | grep mpm; php artisan storage:link --force || true; php artisan config:cache; apache2-foreground"]

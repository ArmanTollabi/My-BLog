# ---------- مرحله ۱: ساخت فرانت‌اند (Vite / Tailwind) ----------
FROM node:22-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# ---------- مرحله ۲: اپ نهایی با PHP 8.4 (بدون Apache) ----------
FROM php:8.4-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libzip-dev libicu-dev libpng-dev libjpeg-dev libfreetype6-dev libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql bcmath intl zip gd mbstring opcache pcntl \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

COPY . .
COPY --from=assets /app/public/build ./public/build
RUN composer dump-autoload --optimize --no-dev \
    && chmod -R 775 storage bootstrap/cache

# چند پروسه برای پاسخ هم‌زمان (سرور داخلی PHP به‌صورت پیش‌فرض تک‌پروسه است)
ENV PHP_CLI_SERVER_WORKERS=4
ENV PORT=8080

CMD ["sh", "-c", "php artisan migrate --force; echo '>>> MIGRATE DONE'; php artisan storage:link --force || true; php artisan config:cache; echo '>>> STARTING SERVER on port' ${PORT:-8080}; exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]

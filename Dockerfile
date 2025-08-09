FROM dunglas/frankenphp:latest

WORKDIR /app

COPY . .

RUN install-php-extensions pdo pdo_mysql mbstring exif pcntl bcmath

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm composer-setup.php

RUN composer install --no-dev --optimize-autoloader

RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8000

CMD php artisan storage:link && php artisan octane:start --server=frankenphp --host=0.0.0.0 --port=8000

FROM unit:1.34.1-php8.3
RUN apt update && apt install -y \
    curl unzip git libicu-dev libzip-dev libpng-dev libjpeg-dev \
    libfreetype6-dev libssl-dev libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
         pcntl opcache pdo pdo_mysql pdo_pgsql pgsql intl zip gd exif ftp bcmath \
    && pecl install redis \
    && docker-php-ext-enable redis

RUN echo "opcache.enable=1" > /usr/local/etc/php/conf.d/custom.ini \
    && echo "opcache.jit=tracing" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "opcache.jit_buffer_size=256M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "memory_limit=512M" >> /usr/local/etc/php/conf.d/custom.ini \        
    && echo "upload_max_filesize=64M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "post_max_size=64M" >> /usr/local/etc/php/conf.d/custom.ini

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
WORKDIR /var/www/html
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache
COPY . .
RUN composer install --prefer-dist --optimize-autoloader --no-interaction

# Only add storage link if it doesn't break things
RUN php artisan storage:link || true

# Fix all permissions at the end
RUN chown -R unit:unit /var/www/html && chmod -R 755 /var/www/html
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

COPY unit.json /docker-entrypoint.d/unit.json
EXPOSE 8000
CMD ["unitd", "--no-daemon"]
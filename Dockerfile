FROM php:8.4-fpm

WORKDIR /var/www

#Dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    libxslt-dev \
    libexif-dev \
    libgd-dev \
    libjpeg-dev \
    libmcrypt-dev \
    libreadline-dev \
    libsqlite3-dev \
    libmagickwand-dev \
    imagemagick \
    libgmp-dev \
    libpq-dev \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        intl \
        gd \
        exif \
        zip \
    && docker-php-ext-enable \
        intl \
        gd \
        exif

#Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN mkdir -p /var/www/html/storage/logs

# Copy configs
COPY nginx/default.conf /etc/nginx/sites-available/default
COPY supervisord.conf /etc/supervisord.conf

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]

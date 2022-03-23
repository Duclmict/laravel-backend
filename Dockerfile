FROM php:7.2-apache

RUN apt-get update

RUN apt-get install -y \
    cron\
    git \
    zip \
    curl \
    sudo \
    unzip \
    libicu-dev \
    libbz2-dev \
    libpng-dev \
    libjpeg-dev \
    libmcrypt-dev \
    libreadline-dev \
    libfreetype6-dev \
    g++\
    cron

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN a2enmod rewrite headers

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN docker-php-ext-install \
    bz2 \
    intl \
    gd \
    iconv \
    bcmath \
    opcache \
    calendar \
    mbstring \
    pdo_mysql \
    zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ARG uid
RUN useradd -G www-data,root -u $uid -d /home/devuser devuser
RUN mkdir -p /home/devuser/.composer && \
    chown -R devuser:devuser /home/devuser

ARG log_folder
RUN mkdir -p $log_folder && chmod 777 $log_folder/../ && \
chmod 777 $log_folder/../* && chown www-data:www-data $log_folder/../*

COPY . /var/www/html
#RUN composer install && php artisan key:generate

# # Copy crontab file to the cron.d directory
# COPY crontab /etc/cron.d/crontab

# # Create the log file to be able to run tail
# RUN touch /var/log/cron.log

# # Give execution rights on the cron job
# RUN chmod 0644 /etc/cron.d/crontab

# # Apply cron job
# RUN crontab /etc/cron.d/crontab
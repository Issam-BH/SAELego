FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    gcc \
    default-jdk \
    libmariadb-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo pdo_mysql mysqli gd
RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf
RUN sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Повністю вимикаємо оптимізацію та кешування файлів у Apache
RUN echo "EnableSendfile Off" >> /etc/apache2/apache2.conf
RUN echo "EnableMMAP Off" >> /etc/apache2/apache2.conf

# Налаштовуємо PHP для розробки (вимикаємо OPcache)
RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini && \
    echo "opcache.enable=0" >> /usr/local/etc/php/php.ini

WORKDIR /var/www/html
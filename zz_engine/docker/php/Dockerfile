FROM php:7.3-apache

RUN apt-get update -y \
&& apt-get install --no-install-recommends -y git zip unzip libfreetype6-dev libjpeg62-turbo-dev libpng-dev zlib1g-dev libicu-dev g++

RUN pecl install xdebug-2.7.2 \
&& docker-php-ext-enable xdebug \
&& echo "xdebug.remote_enable=1" >> /usr/local/etc/php/php.ini

RUN docker-php-ext-configure pdo_mysql --with-pdo-mysql \
&& docker-php-ext-install -j$(nproc) pdo_mysql \
&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
&& docker-php-ext-install gd \
&& docker-php-ext-configure intl \
&& docker-php-ext-install intl \
&& docker-php-ext-install opcache \
&& true

RUN a2enmod rewrite \
&& a2enmod ssl \
&& a2enmod expires \
&& a2ensite default-ssl

RUN mkdir /tmp/xdebug_out \
&& chmod 777 /tmp/xdebug_out \
&& chown :www-data /tmp/xdebug_out/ \
&& true

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN useradd -u 1000 -g www-data -d /var/www www-data-docker-host

EXPOSE 443

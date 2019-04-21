FROM php:7.3-apache

RUN pecl install xdebug-2.7.0RC1
RUN docker-php-ext-enable xdebug
RUN echo "xdebug.remote_enable=1" >> /usr/local/etc/php/php.ini

RUN apt-get update -y
RUN apt-get install --no-install-recommends -y git unzip libfreetype6-dev libjpeg62-turbo-dev libpng-dev zlib1g-dev libicu-dev g++
RUN docker-php-ext-configure pdo_mysql --with-pdo-mysql \
&& docker-php-ext-install -j$(nproc) mysqli \
&& docker-php-ext-install -j$(nproc) pdo_mysql \
&& docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
&& docker-php-ext-install gd

RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl


RUN a2enmod rewrite
RUN a2ensite default-ssl
RUN a2enmod ssl

RUN docker-php-ext-install opcache

RUN mkdir /tmp/xdebug_out
RUN chmod 777 /tmp/xdebug_out
RUN chown :www-data /tmp/xdebug_out/

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN useradd -u 1000 -g www-data -d /var/www www-data-docker-host

EXPOSE 443

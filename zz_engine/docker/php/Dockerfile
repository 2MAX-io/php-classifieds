FROM php:7.4-apache

RUN true \
&& curl -sL https://deb.nodesource.com/setup_12.x | bash \
&& apt-get update -y \
&& apt-get install --no-install-recommends -y \
zip \
unzip \
libfreetype6-dev \
libjpeg62-turbo-dev \
libpng-dev \
zlib1g-dev \
libicu-dev \
g++ \
default-mysql-client \
nodejs \
git \
&& npm install --global gulp-cli \
&& true

RUN true \
&& docker-php-ext-configure pdo_mysql --with-pdo-mysql \
&& docker-php-ext-install -j$(nproc) pdo_mysql \
&& docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
&& docker-php-ext-install gd \
&& docker-php-ext-configure intl \
&& docker-php-ext-install intl \
&& docker-php-ext-install opcache \

&& a2enmod rewrite \
&& a2enmod ssl \
&& a2enmod expires \
&& a2ensite default-ssl \

&& useradd -u 1000 -g www-data -d /var/www www-data-docker-host \
&& true

COPY --from=composer:1 /usr/bin/composer /usr/bin/composer

ARG WITH_XDEBUG=false
RUN if [ $WITH_XDEBUG = "true" ] ; then \
    pecl install xdebug; \
    docker-php-ext-enable xdebug; \
    echo "xdebug.remote_enable=1" >> /usr/local/etc/php/php.ini; \
    mkdir /tmp/xdebug_out; \
    chmod 777 /tmp/xdebug_out; \
    chown :www-data /tmp/xdebug_out/; \
fi;

#COPY ./../../composer.json zz_engine
#RUN bash -c "composer install -d /var/www/html/zz_engine --optimize-autoloader"

RUN true \
&& curl -sL https://deb.nodesource.com/setup_12.x | bash \
&& npm install gulp \
&& true

EXPOSE 443

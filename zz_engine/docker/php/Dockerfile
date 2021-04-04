FROM php:7.4-apache

RUN true \
&& curl -sL https://deb.nodesource.com/setup_14.x | bash \
&& curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - \
&& echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list \
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
yarn \
&& true

RUN true \
&& docker-php-ext-configure pdo_mysql --with-pdo-mysql \
&& docker-php-ext-install -j$(nproc) pdo_mysql \
&& docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
&& docker-php-ext-install gd \
&& docker-php-ext-configure intl \
&& docker-php-ext-install intl \
&& docker-php-ext-install opcache \
&& true \
&& a2enmod rewrite \
&& a2enmod ssl \
&& a2enmod expires \
&& a2ensite default-ssl \
&& true \
&& useradd -u 1000 -g www-data -d /var/www www-data-docker-host \
&& chown -R 1000:1000 /var/www \
&& true

COPY --from=composer:1 /usr/bin/composer /usr/bin/composer

ARG WITH_XDEBUG=false
RUN if [ $WITH_XDEBUG = "true" ] ; then \
    pecl install xdebug-3.0.3; \
    docker-php-ext-enable xdebug; \
#    echo "xdebug.mode=debug" >> /usr/local/etc/php/php.ini; \
    mkdir /tmp/xdebug_out; \
    chmod 777 /tmp/xdebug_out; \
    chown :www-data /tmp/xdebug_out/; \
fi;

#COPY ./../../composer.json zz_engine
#RUN bash -c "composer install -d /var/www/html/zz_engine --optimize-autoloader"

RUN apt-get install --no-install-recommends -y sudo

EXPOSE 443

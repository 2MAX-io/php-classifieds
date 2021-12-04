## PHP Classifieds Ads by 2MAX.io - Preview of code

that can be bought on:
https://php-classifieds.xyz/

__no commercial and for profit use including display of advertisement allowed until license has been bought__

---
## Requirements

PHP 7.3+, recommended PHP 8+ with opcache

MYSQL 5.6+, recommended MYSQL 8

Full requirements:

https://php-classifieds.xyz/requirements/

---
## Installation

Information about installation can be found:

https://php-classifieds.xyz/installation/

to start installation enter `example.com/install` directory

if you want install from repository (not pre build package) you must build assets and install composer dependencies:

```
composer install --no-scripts --classmap-authoritative --quiet --no-dev --no-interaction -d zz_engine

bash zz_engine/dev/bin/build_assets.sh
```

---
##Documentation:

https://php-classifieds.xyz/documentation/

## Development

to build assets
```
bash zz_engine/dev/bin/build_assets.sh
```

to install composer dependencies (for development, instruction for production install is in __Installation__ section of this readme):
```
composer install -d zz_engine --optimize-autoloader
```

it is recommended to develop on domain:
```
https://classifieds.localhost/
```

and using docker, because default config assumes that

config for database and other:
```
zz_engine/.env.local.php
```

builder of installation package can be found here:
```
zz_engine/dev/bin/DEV_build_installer.sh
```

docker config could be found in:

```
zz_engine/docker
```

and started using commands:

```
# without xdebug
(cd zz_engine/docker && export WITH_XDEBUG=false && docker-compose up --build)

# with xdebug
(cd zz_engine/docker && export WITH_XDEBUG=true && docker-compose up --build)
```

to use valid SSL certificate on you dev machine follow instructions:
```
zz_engine/docker/php/ssl/_readme_install_ssl_https_mkcert.txt
```

Web UI for testing emails send for development

http://classifieds.localhost:8025/

#!/bin/sh

GREEN='\033[0;32m'
NC='\033[0m' # No Color
set -o xtrace
set -e

cd /home/j4uoglosze/domains/ogloszenia.jaslo4u.pl/public_html
git pull
rm -r /home/j4uoglosze/domains/ogloszenia.jaslo4u.pl/public_html/zz_engine/var/cache/prod
wget -O /dev/null https://www.ogloszenia.jaslo4u.pl/

/usr/local/php73/bin/php /home/j4uoglosze/domains/ogloszenia.jaslo4u.pl/public_html/zz_engine/composer.phar install -d zz_engine --classmap-authoritative

wget -O /dev/null https://www.ogloszenia.jaslo4u.pl/

printf "${GREEN}CONSIDER IT DONE${NC}\n"

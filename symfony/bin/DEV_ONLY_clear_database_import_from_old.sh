#!/usr/bin/env bash

docker exec classifieds_mysql bash -c "mysqldump classifieds > /sql/ogl_$(date +%Y-%m-%d_%H%M%S)_old.sql"
docker exec classifieds_mysql mysql -e "DROP DATABASE admin_ogloszenia"
docker exec classifieds_mysql mysql -e "CREATE DATABASE admin_ogloszenia"
docker exec classifieds_mysql bash -c "mysql admin_ogloszenia < /sql/admin_ogloszenia.sql"

docker exec classifieds_php bash -c "php symfony/bin/importer_export_from_old.php /var/www/html/symfony/docker/mysql/sql/importer_export_from_old.csv"
docker exec classifieds_php bash -c "php symfony/bin/importer_import_to_new.php /var/www/html/symfony/docker/mysql/sql/importer_export_from_old.csv /var/www/html/symfony/docker/mysql/sql/importer_import_to_new.sql"

docker exec classifieds_mysql bash -c "mysql classifieds < /sql/dev/truncate_listings.sql"
time docker exec classifieds_mysql bash -c "mysql classifieds < /sql/importer_import_to_new.sql"

docker exec classifieds_mysql bash -c "mysqldump classifieds > /sql/ogl_$(date +%Y-%m-%d_%H%M%S)_new.sql"

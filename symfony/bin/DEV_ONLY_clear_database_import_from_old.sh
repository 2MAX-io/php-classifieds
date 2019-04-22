#!/usr/bin/env bash

php symfony/bin/importer_export_from_old.php /var/www/html/symfony/docker/mysql/sql/importer_export_from_old.csv
php symfony/bin/importer_import_to_new.php /var/www/html/symfony/docker/mysql/sql/importer_export_from_old.csv /var/www/html/symfony/docker/mysql/sql/importer_import_to_new.sql

mysql < symfony/dev/sql_snippets/truncate_listings.sql

time mysql classifieds < /sql/importer_import_to_new.sql

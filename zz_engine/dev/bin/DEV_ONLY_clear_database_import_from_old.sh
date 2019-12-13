#!/usr/bin/env bash

set -o xtrace

docker exec classifieds_mysql bash -c "mysqldump classifieds > /sql/ogl_$(date +%Y-%m-%d_%H%M%S)_old.sql"
docker exec classifieds_mysql mysql -e "DROP DATABASE admin_ogloszenia"
docker exec classifieds_mysql mysql -e "CREATE DATABASE admin_ogloszenia"
time docker exec classifieds_mysql bash -c "mysql admin_ogloszenia < /sql/admin_ogloszenia.sql"
docker exec classifieds_mysql mysql admin_ogloszenia -e "UPDATE o_ogloszenia SET opis_d = SUBSTR(opis_d, 1, 9900) WHERE LENGTH(opis_d) > 10000;"
#time docker exec classifieds_mysql mysql admin_ogloszenia -e "alter table o_ogloszenia modify opis_d varchar(11000) not null, ALGORITHM=COPY;"

docker exec classifieds_php bash -c "php zz_engine/dev/bin/importer_export_from_old.php /var/www/html/zz_engine/docker/mysql/sql/importer_export_from_old.csv"
docker exec classifieds_php bash -c "php zz_engine/dev/bin/importer_create_sql_for_new_classified.php /var/www/html/zz_engine/docker/mysql/sql/importer_export_from_old.csv /var/www/html/zz_engine/docker/mysql/sql/importer_import_to_new.sql"

docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE listing_file"
docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE listing_view"
docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE listing"
docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE user_balance_change"
docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE zzzz_listing_police_log"
docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE listing_custom_field_value"
docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE payment_for_featured_package"
docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE payment"
docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE payment_for_balance_top_up"

docker exec classifieds_php php zz_engine/bin/console doctrine:schema:update --force

#docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE admin"
#docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE category"
#docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE custom_field"
#docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE custom_field_join_category"
#docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE custom_field_option"
#docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE featured_package"
#docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE featured_package_for_category"
#docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE listing"
#docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE listing_custom_field_value"
#docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE listing_file"
#docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE listing_view"
#docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE page"
#docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE payment"
#docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE payment_for_balance_top_up"
#docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE payment_for_featured_package"
#docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE setting"
#docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE user"
#docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE user_balance_change"
#docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE zzzz_listing_police_log"
#docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE zzzz_system_log"
#docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE zzzz_token"
#docker exec classifieds_mysql mysql classifieds -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE zzzz_token_field"

time docker exec classifieds_mysql bash -c "mysql --max_allowed_packet=1G classifieds < /sql/importer_import_to_new.sql"

docker exec classifieds_php php zz_engine/bin/console app:cron:main

docker exec classifieds_mysql bash -c "mysqldump classifieds > /sql/ogl_$(date +%Y-%m-%d_%H%M%S)_new.sql"

bash /home/u/PhpstormProjects/classifieds/zz_engine/bin/DEV_pull_images.sh

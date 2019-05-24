#!/usr/bin/env bash

cd /home/lpodnogl/domains/oglnew-hjk1.lpodolski.com/public_html/

mysql lpodnogl_ogl -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE admin"
mysql lpodnogl_ogl -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE category"
mysql lpodnogl_ogl -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE custom_field"
mysql lpodnogl_ogl -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE custom_field_category"
mysql lpodnogl_ogl -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE custom_field_option"
mysql lpodnogl_ogl -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE featured_package"
mysql lpodnogl_ogl -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE featured_package_for_category"
mysql lpodnogl_ogl -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE listing"
mysql lpodnogl_ogl -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE listing_custom_field_value"
mysql lpodnogl_ogl -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE listing_file"
mysql lpodnogl_ogl -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE listing_view"
mysql lpodnogl_ogl -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE page"
mysql lpodnogl_ogl -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE setting"
mysql lpodnogl_ogl -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE user"
mysql lpodnogl_ogl -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE user_balance_change"
mysql lpodnogl_ogl -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE zzzz_listing_police_log"
mysql lpodnogl_ogl -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE zzzz_token"
mysql lpodnogl_ogl -e "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE zzzz_token_field"

mysql lpodnogl_ogl < $(ls -t zzzz_engine/var/ogl_*.sql | head -1)



git pull
/usr/local/php73/bin/php /home/lpodnogl/domains/oglnew-hjk1.lpodolski.com/composer.phar install -d /home/lpodnogl/domains/oglnew-hjk1.lpodolski.com/public_html/zzzz_engine
rm -r /home/lpodnogl/domains/oglnew-hjk1.lpodolski.com/public_html/zzzz_engine/var/cache/prod/
/usr/local/php73/bin/php zzzz_engine/bin/console d:schema:update --force
/usr/local/php73/bin/php /home/lpodnogl/domains/oglnew-hjk1.lpodolski.com/composer.phar dump-env prod -d /home/lpodnogl/domains/oglnew-hjk1.lpodolski.com/public_html/zzzz_engine
/usr/local/php73/bin/php /home/lpodnogl/domains/oglnew-hjk1.lpodolski.com/composer.phar dump-autoload --optimize -d /home/lpodnogl/domains/oglnew-hjk1.lpodolski.com/public_html/zzzz_engine
/usr/local/php73/bin/php /home/lpodnogl/domains/oglnew-hjk1.lpodolski.com/public_html/zzzz_engine/bin/console app:cron:main

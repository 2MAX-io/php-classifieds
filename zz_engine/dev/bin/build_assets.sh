#!/usr/bin/env bash

php zz_engine/bin/console fos:js-routing:dump --format json --target asset/backendGenerated/fosjsrouting/routes.json
php zz_engine/bin/console bazinga:js-translation:dump --merge-domains asset/backendGenerated/bazingajstranslation
php zz_engine/bin/console app:dev:generate-paramenum-for-js

if [[ ! -d /var/www/html/zz_engine/node_modules ]]; then
  (cd /var/www/html/zz_engine && npm install)
fi

(cd /var/www/html/zz_engine && yarn encore production)

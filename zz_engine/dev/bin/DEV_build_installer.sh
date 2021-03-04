#!/usr/bin/env bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
PROJECT_DIR=${DIR}/../../..
GIT_HASH=$(git rev-parse HEAD)
INSTALLER_DIR=zz_engine/var/installer/installer_$(date +%Y-%m-%d_%H%M%S)_${GIT_HASH::8}
INSTALLER_ZIP=installer_$(date +%Y-%m-%d_%H%M%S)_${GIT_HASH::8}.zip

git checkout-index -a -f --prefix=${INSTALLER_DIR}/

rm -r ${INSTALLER_DIR}/.idea
rm -r ${INSTALLER_DIR}/zz_engine/dev
rm -r ${INSTALLER_DIR}/zz_engine/docker

#rm ${INSTALLER_DIR}/.gitignore
#rm ${INSTALLER_DIR}/jsconfig.json

cp ${PROJECT_DIR}/install/data/example/listing.large.sql ${INSTALLER_DIR}/install/data/example/listing.large.sql

mkdir -p ${INSTALLER_DIR}/zz_engine/var/cache
mkdir -p ${INSTALLER_DIR}/zz_engine/var/cache/upgrade
mkdir -p ${INSTALLER_DIR}/zz_engine/var/cache/prod

cd ${INSTALLER_DIR}

composer install --no-scripts --classmap-authoritative --quiet --no-dev --no-interaction -d zz_engine

zip -rq9 ${INSTALLER_ZIP} .

mv ${INSTALLER_ZIP} ${PROJECT_DIR}/zz_engine/var/installer
rm -r ${PROJECT_DIR}/${INSTALLER_DIR}

GREEN='\033[0;32m'
NC='\033[0m' # No Color
printf "${GREEN}CONSIDER IT DONE${NC}\n"

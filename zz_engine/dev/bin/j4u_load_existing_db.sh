#!/usr/bin/env bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
PROJECT_DIR=${DIR}/../..

cd ${PROJECT_DIR}/var/input
pwd

tar -xzf j4u_ogloszenia.sql.tar.gz
mysql classifieds < ${PROJECT_DIR}/var/input/backup/*/mysql/j4u_ogloszenia.sql
pwd

#!/usr/bin/env bash

php zz_engine/bin/console doctrine:schema:create --dump-sql > install/data/schema.sql

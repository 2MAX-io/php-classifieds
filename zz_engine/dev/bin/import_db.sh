#!/usr/bin/env bash

set -o xtrace

mysql -e "CREATE DATABASE admin_ogloszenia"
mysql admin_ogloszenia < /sql/admin_ogloszenia.sql

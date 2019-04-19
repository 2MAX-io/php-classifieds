#!/usr/bin/env bash

mysql -e "CREATE DATABASE admin_ogloszenia"
mysql admin_ogloszenia < /sql/admin_ogloszenia.sql

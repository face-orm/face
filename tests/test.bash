#!/bin/bash

SCRIPTFILE=$(readlink -f "$0")
SCRIPTDIR=$(dirname "$SCRIPTFILE")

mysql -u root -e 'create database `lemon-test`;'
mysql -u root  < "$SCRIPTDIR/schemas.sql"

phpunit --debug -c phpunit.dist.xml --coverage-clover build/logs/clover.xml

./vendor/bin/test-reporter
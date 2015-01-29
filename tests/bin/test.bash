#!/bin/bash

trap 'exit' ERR

SCRIPTFILE=$(readlink -f "$0")
SCRIPTDIR=$(dirname "$SCRIPTFILE")

mysql -u root -e 'create database `lemon-test`;'
mysql -u root  < "$SCRIPTDIR/../res/schemas.sql"

phpunit --debug -c phpunit.dist.xml --coverage-clover "$SCRIPTDIR/../../build/logs/clover.xml"

./vendor/bin/test-reporter
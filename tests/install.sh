#!/bin/sh

mysql -uroot  -e 'DROP DATABASE IF EXISTS `lemon-test`;'
mysql -uroot  -e 'CREATE DATABASE IF NOT EXISTS `lemon-test`;'
mysql -uroot  -D 'lemon-test' < schemas.sql
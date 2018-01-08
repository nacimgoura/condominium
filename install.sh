#!/usr/bin/env bash

npm install
composer install
docker-compose up -d
sleep 5
docker-compose start
./init_database.sh
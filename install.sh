#!/usr/bin/env bash

npm install
composer install
docker-compose up -d
sleep 5
docker-compose start
php bin/console doctrine:database:create -n
php bin/console doctrine:migrations:diff -n
php bin/console doctrine:migrations:migrate -n
php bin/console doctrine:fixtures:load -n
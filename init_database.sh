#!/usr/bin/env bash

php bin/console doctrine:database:create -n
php bin/console doctrine:migrations:diff -n
php bin/console doctrine:migrations:migrate -n
php bin/console doctrine:fixtures:load -n
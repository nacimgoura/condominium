# Application de gestion de copropriété


## Prérequis
 - PHP 7.2.0
 - docker
 - docker-compose
 - nodeJS 8.x.x


## Installation
```
npm install
composer
docker-compose up
```

## Usage
```
docker-compose start
php bin/console doctrine:fixtures:load
npm run dev
php bin/console server:run
```
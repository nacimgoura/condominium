# Application de gestion de copropriété

Cette application à été réalisée en Symfony 4.0.3

## Prérequis
 - PHP 7.2.0
 - docker
 - docker-compose
 - nodeJS 8.x.x

## Installation
```
./install.sh
```

## Usage

- Pour installer les dépendances
```
npm install
composer install
```

- Pour lancer docker (instance mysql et phpmyadmin)
```
docker-compose up -d
docker-compose start
```

- Pour créer la base avec le bon jeu de données
```
docker-compose up -d
php bin/console doctrine:database:create -n
php bin/console doctrine:migrations:diff -n
php bin/console doctrine:migrations:migrate -n
php bin/console doctrine:fixtures:load -n
```

- Pour l'accès au projet `http://localhost:8000/`

- Pour l'accès à phpmyadmin
`http://localhost:8080/`

- Pour l'accès à  mailcatcher
`http://localhost:1080/`
# Symfony

Ce repo contient une application de gestion de formation.
il s'agit d'un projet pédagogique pour la promo 11.

## Prérequis

- Linux, MacOS ou Windows
- Bash
- PHP 8
- composer
- symfony-cli
- MariaDB 10
- docker (optionnel)

## Intallation

```
git clone https://github.com/EmmaCremeaux/symfony
cd symfony
composer install
```

Créez une base de données et un utilisateur dédié pour cette base de données.

## Configuration

Créez un fichier `.env.local` à la racine du projet :

```
APP_ENV=dev
APP_DEBUG=true
APP_SECRET=92a603d45e1ca0478f4d36a24caf04a7
DATABASE_URL="mysql://symfony:123@127.0.0.1:3306/symfony?serverVersion=mariadb-10.6.12&charset=utf8mb4"
```

Pensez à changer la variable `APP_SECRET` et les code d'accès `123` dans la variable `DATABASE_URL`.

**ATTENTION : `APP_SECRET` doit être une chaîne de caractère de 32 caractère en hexadécimal.**

## Migration et fixtures

pour que l'application soit utilisable, vous devez créer le schéma de base de données et charger les données :

```
bin/dofilo.sh
```

## Utilisation

Lancez le serveur web de développement : 

```
symfony serve
```

puis ouvrez la page suivante : [home page](https://localhost:8000)

## Mentions Légales

Ce projet est sous licence MIT.

la licence est disponible ici : [MIT LICENCE](LICENCE).

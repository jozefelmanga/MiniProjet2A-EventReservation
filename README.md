# Gestion d'événements et réservations (Symfony 8)

## Description du projet

- Application web Symfony 8 permettant aux utilisateurs inscrits de parcourir la liste des événements, d'ouvrir une fiche détaillée et de réserver en fournissant nom, email et téléphone.
- Les réservations sont bloquées si l'événement est passé ou si le quota de places est à zéro, et chaque réservation décrémente le nombre de places restantes.
- Espace administrateur distinct (ROLE_ADMIN) : création, édition, suppression d'événements, consultation des réservations par événement et tableau de bord dédié.
- Authentification par formulaires pour les utilisateurs et les administrateurs, avec un compte admin de démonstration chargé via les fixtures.

## Technologies utilisées

- PHP 8.4, Symfony 8 (Framework, Security, Twig, Form, Validator)
- Doctrine ORM 3.6 pour la couche de persistance
- Base de données : MySQL 8 par défaut (migrations générées pour MySQL) ; Docker Compose fourni pour PostgreSQL 16 si vous regénérez les migrations
- Twig pour les vues server-side
- Doctrine Fixtures Bundle (données de démo) et Symfony Maker (outillage dev)

## Consignes d'installation

1. **Prérequis** : PHP >=8.4, Composer, une base MySQL 8 (recommandé pour utiliser les migrations actuelles) ou Docker Compose. Symfony CLI est pratique mais facultatif.
2. **Installer les dépendances** :
    ```bash
    composer install
    ```
3. **Configurer l'environnement** : copier `.env` en `.env.local`, définir `APP_SECRET` et `DATABASE_URL`.
    - Exemple MySQL (aligné sur les migrations existantes) :
        ```env
        DATABASE_URL="mysql://root:motdepasse@127.0.0.1:3306/event_reservation?serverVersion=8.0&charset=utf8mb4"
        ```
    - Exemple PostgreSQL via Docker Compose (nécessite de regénérer une migration adaptée) :
        ```env
        DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=16&charset=utf8"
        ```
        Puis exécuter `php bin/console doctrine:migrations:diff` pour produire une migration compatible Postgres.
4. **Démarrer la base** :
    - MySQL local : créer la base si besoin `php bin/console doctrine:database:create`.
    - Ou avec Docker Compose (service Postgres fourni) :
        ```bash
        docker compose up -d database
        ```
5. **Appliquer le schéma** :
    ```bash
    php bin/console doctrine:migrations:migrate
    ```
6. **Charger les données de démo** (admin `admin` / `admin123`) :
    ```bash
    php bin/console doctrine:fixtures:load
    ```
7. **Lancer l'application** :
    - Avec Symfony CLI : `symfony server:start -d`
    - Ou PHP natif : `php -S 127.0.0.1:8000 -t public`

Points d'accès :

- Front utilisateur : `/register`, `/login`, `/events`, `/events/{id}` (nécessite authentification).
- Administration : `/admin/login`, `/admin/dashboard`, gestion des événements sous `/admin/events`.

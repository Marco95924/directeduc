#!/usr/bin/env bash
# Arrêter le script en cas d'erreur
set -o errexit

# Installer les dépendances PHP
composer install --no-dev --optimize-autoloader

# Vider et mettre en cache la configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Exécuter les migrations de base de données
php artisan migrate --force
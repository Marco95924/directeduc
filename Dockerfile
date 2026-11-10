FROM php:8.2-apache

# Installation des dépendances système et extensions nécessaires pour Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Nettoyage
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Installation des extensions PHP (PDO MySQL, Mbstring, Exif, PCNL, BCMath, GD)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Activation de mod_rewrite d'Apache pour les routes Laravel
RUN a2enmod rewrite

# Configuration du dossier racine d'Apache vers le dossier "public" de Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# Copie de Composer depuis son image officielle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définition du dossier de travail dans le conteneur
WORKDIR /var/www/html

# Copie des fichiers du projet dans le conteneur
COPY . /var/www/html

# Installation des dépendances PHP via Composer (sans les dev pour la production)
RUN composer install --no-dev --optimize-autoloader

# Attribution des permissions sur les dossiers de stockage et de cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
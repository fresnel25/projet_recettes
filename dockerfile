FROM php:8.2-cli

# Installer dépendances système et extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev libicu-dev libonig-dev iputils-ping \
    && docker-php-ext-install pdo pdo_mysql zip intl mbstring
# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Dossier de travail
WORKDIR /var/www/html

# En dev, on ne copie pas encore le code ici
# car il sera monté via docker-compose (bind mount)

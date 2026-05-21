FROM php:8.2-apache

# Installer l'extension PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Activer mod_rewrite pour les URLs propres
RUN a2enmod rewrite

# Copier tout le code dans le conteneur
COPY src/ /var/www/html/src/

# Le document root pointe vers src/public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/src/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Autoriser les .htaccess
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Permissions correctes
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
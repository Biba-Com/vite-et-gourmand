FROM php:8.2-apache

RUN a2dismod mpm_event || true && a2enmod mpm_prefork

RUN docker-php-ext-install pdo pdo_mysql

RUN a2enmod rewrite

COPY src/ /var/www/html/src/

ENV APACHE_DOCUMENT_ROOT=/var/www/html/src/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["sh", "-c", "a2dismod mpm_event; a2enmod mpm_prefork; apache2-foreground"]

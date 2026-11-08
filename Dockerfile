FROM php:8.2-apache

# Install PDO and MySQLi extensions for PHP
RUN docker-php-ext-install pdo_mysql mysqli

# Enable Apache mod_rewrite for .htaccess support
RUN a2enmod rewrite
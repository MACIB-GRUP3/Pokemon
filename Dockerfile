# Usamos la imagen base oficial de PHP con Apache
FROM php:8.1-apache

# 1. Hardening: Instalamos extensiones necesarias y limpiamos para reducir superficie de ataque
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# 2. Hardening: Ocultar versión del servidor (Security through obscurity)
RUN echo "ServerTokens Prod" >> /etc/apache2/apache2.conf && \
    echo "ServerSignature Off" >> /etc/apache2/apache2.conf

# 3. Copiamos el código de la aplicación al contenedor
COPY . /var/www/html/

# 4. Hardening: Copiamos configuración de seguridad específica (.htaccess)
COPY .htaccess /var/www/html/.htaccess

# 5. Hardening: Permisos y Usuario No Privilegiado
# Cambiamos el dueño de los archivos a www-data (usuario estándar de Apache)
RUN chown -R www-data:www-data /var/www/html

# Apache por defecto escucha en el puerto 80, que requiere root.
# Cambiamos al puerto 8080 para poder correr como usuario normal.
RUN sed -i 's/80/8080/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Cambiamos al usuario www-data. A partir de aquí, el contenedor es seguro.
USER www-data

# Exponemos el nuevo puerto
EXPOSE 8080

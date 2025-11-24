#!/bin/bash

# Nombre de la red y contenedores (deben coincidir con zap-plan.yaml)
NETWORK_NAME="cicd-network"
DB_CONTAINER="pokemon-db"
APP_CONTAINER="pokemon-php-app"
ZAP_CONTAINER="zap-pokemon"

echo "=== 1. Preparando entorno limpio ==="
# Limpiamos contenedores previos si existen
docker rm -f $DB_CONTAINER $APP_CONTAINER $ZAP_CONTAINER 2>/dev/null
docker network rm $NETWORK_NAME 2>/dev/null

# Creamos la red
docker network create $NETWORK_NAME

echo "=== 2. Levantando Base de Datos ==="
# Iniciamos MySQL (Igual que en tu Jenkinsfile)
docker run -d \
    --name $DB_CONTAINER \
    --network $NETWORK_NAME \
    -e MYSQL_ALLOW_EMPTY_PASSWORD=yes \
    mysql:5.7 --max_allowed_packet=64M

echo "⏳ Esperando a que MySQL esté listo..."
sleep 15 # Espera de seguridad inicial

# Copiamos e importamos el SQL
docker cp pokewebapp.sql $DB_CONTAINER:/tmp/pokewebapp.sql
# Esperamos hasta que MySQL responda (loop de verificación)
until docker exec $DB_CONTAINER mysqladmin ping -h localhost --silent; do
    echo "Espera base de datos..."
    sleep 2
done

# Creamos la estructura
docker exec $DB_CONTAINER mysql -uroot -e "CREATE DATABASE IF NOT EXISTS Pokewebapp;"
docker exec $DB_CONTAINER mysql -uroot Pokewebapp -e "source /tmp/pokewebapp.sql"

echo "=== 3. Levantando Aplicación Web ==="
# Iniciamos la App PHP montando el código actual
docker run -d \
    --name $APP_CONTAINER \
    --network $NETWORK_NAME \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    php:8.1-apache

# Instalamos extensiones necesarias (mysqli)
sleep 5
docker exec $APP_CONTAINER bash -c "docker-php-ext-install mysqli && docker-php-ext-enable mysqli && a2enmod rewrite headers && apache2ctl graceful"

echo "=== 4. Ejecutando OWASP ZAP ==="
# Creamos carpeta para reportes si no existe
mkdir -p zap-reports
chmod 777 zap-reports

# Ejecutamos ZAP conectado a la misma red
# Nota: Usamos el zap-plan.yaml que configuramos anteriormente
docker run --rm \
    --name $ZAP_CONTAINER \
    --network $NETWORK_NAME \
    -v "$(pwd)/zap-reports:/zap/wrk:rw" \
    -v "$(pwd)/zap-plan.yaml:/zap/plan.yaml:ro" \
    -t ghcr.io/zaproxy/zaproxy:stable \
    zap.sh -cmd -autorun /zap/plan.yaml

echo "=== 5. Limpieza Final ==="
# Destruimos el entorno al terminar
docker stop $DB_CONTAINER $APP_CONTAINER
docker rm $DB_CONTAINER $APP_CONTAINER
docker network rm $NETWORK_NAME

echo "✅ Proceso completado. Revisa el reporte en la carpeta zap-reports."

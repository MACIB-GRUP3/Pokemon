#!/bin/bash

# Configuración
REPO_URL="https://github.com/MACIB-GRUP3/Pokemon.git" #
TEMP_DIR="temp_pokemon_app"
NETWORK_NAME="cicd-network"
DB_CONTAINER="pokemon-db"
APP_CONTAINER="pokemon-php-app"
ZAP_CONTAINER="zap-pokemon"

# Función de limpieza para asegurar que no queden residuos
cleanup() {
    echo "🧹 Limpiando contenedores y archivos temporales..."
    docker stop $DB_CONTAINER $APP_CONTAINER $ZAP_CONTAINER 2>/dev/null
    docker rm -f $DB_CONTAINER $APP_CONTAINER $ZAP_CONTAINER 2>/dev/null
    docker network rm $NETWORK_NAME 2>/dev/null
    rm -rf $TEMP_DIR
    echo "✨ Entorno limpio."
}

# Ejecutar limpieza al inicio y al final (o si hay error)
trap cleanup EXIT

echo "=== 1. Descargando código desde GitHub ==="
# Clonamos el repositorio en una carpeta temporal
if [ -d "$TEMP_DIR" ]; then rm -rf "$TEMP_DIR"; fi
git clone $REPO_URL $TEMP_DIR

echo "=== 2. Inyectando configuraciones corregidas ==="
# IMPORTANTE: Copiamos TUS archivos locales (con los fixes) dentro de la carpeta descargada
# Esto sobrescribe los archivos por defecto del repositorio que podrían estar mal configurados
cp zap-plan.yaml $TEMP_DIR/zap-plan.yaml
cp pokewebapp.sql $TEMP_DIR/pokewebapp.sql

echo "=== 3. Levantando Infraestructura ==="
docker network create $NETWORK_NAME

# Base de datos
docker run -d \
    --name $DB_CONTAINER \
    --network $NETWORK_NAME \
    -e MYSQL_ALLOW_EMPTY_PASSWORD=yes \
    mysql:5.7 --max_allowed_packet=64M

echo "⏳ Esperando a MySQL..."
sleep 10
# Copiamos el SQL modificado al contenedor
docker cp $TEMP_DIR/pokewebapp.sql $DB_CONTAINER:/tmp/pokewebapp.sql

# Esperamos conexión
until docker exec $DB_CONTAINER mysqladmin ping -h localhost --silent; do
    echo "Esperando DB..."
    sleep 2
done

# Cargamos los datos
docker exec $DB_CONTAINER mysql -uroot -e "CREATE DATABASE IF NOT EXISTS Pokewebapp;"
docker exec $DB_CONTAINER mysql -uroot Pokewebapp -e "source /tmp/pokewebapp.sql"

# Aplicación Web (usando el código descargado en TEMP_DIR)
docker run -d \
    --name $APP_CONTAINER \
    --network $NETWORK_NAME \
    -v "$(pwd)/$TEMP_DIR:/var/www/html" \
    -w /var/www/html \
    php:8.1-apache

# Configuración de PHP/Apache
sleep 5
docker exec $APP_CONTAINER bash -c "docker-php-ext-install mysqli && docker-php-ext-enable mysqli && a2enmod rewrite headers && apache2ctl graceful"

echo "=== 4. Ejecutando OWASP ZAP ==="
mkdir -p zap-reports
chmod 777 zap-reports

# Ejecutamos ZAP usando el plan que inyectamos en la carpeta temporal
docker run --rm \
    --name $ZAP_CONTAINER \
    --network $NETWORK_NAME \
    -v "$(pwd)/zap-reports:/zap/wrk:rw" \
    -v "$(pwd)/$TEMP_DIR/zap-plan.yaml:/zap/plan.yaml:ro" \
    -t ghcr.io/zaproxy/zaproxy:stable \
    zap.sh -cmd -autorun /zap/plan.yaml

echo "✅ Escaneo finalizado. Reporte guardado en ./zap-reports"

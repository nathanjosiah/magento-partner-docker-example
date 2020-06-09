#!/bin/bash

info () {
  echo -e "\033[0;34m" $* "\033[0m"
}

DIND_CONTAINER=$(docker run -d --rm --privileged -p 12375:2375 -p 8877:80 -e DOCKER_TLS_CERTDIR="" docker:dind)
export DOCKER_HOST=tcp://localhost:12375

info 'Building Tool'
docker build -t tool ./tool
info 'Building PHP CLI'
docker build -t php-cli ./php-cli
info 'Building PHP FPM'
docker build -t mage-php-fpm ./php-fpm
info 'Building Nginx'
docker build -t mage-nginx ./nginx
info 'Building Mariadb'
docker build -t db ./db

info 'Create volume'
docker volume create --name mage

info 'Create network'
docker network create --driver bridge cicd

info 'Running php-fpm'
PHP_CONTAINER=$(docker run --rm -d \
  -p 9000:9000 \
  --name fpm \
  --network cicd \
  -v mage:/themount\
  mage-php-fpm)
docker exec $PHP_CONTAINER ls -s /themount/magento-ce /var/www/html

info 'Running mariadb'
docker run --rm -d \
  -p 3306:3306 \
  --name db \
  --network cicd \
  -e MYSQL_ROOT_PASSWORD=secretpw \
  -e 'MYSQL_ROOT_HOST=%' \
  -e MYSQL_DATABASE=main \
  db

info 'Running Tool'
docker run --rm \
  --name tool \
  --network cicd \
  --env-file .env \
  -v /var/run/docker.sock:/var/run/docker.sock \
  -v mage:/magento \
  tool \
  somearg

info 'Running Nginx'
MAGENTO_CONTAINER=$(docker run --rm -d \
  --network cicd \
  --name mage-nginx \
  -v mage:/themount mage-nginx
)
docker exec $MAGENTO_CONTAINER mkdir /app
docker exec $MAGENTO_CONTAINER ls -s /themount/magento-ce /app

info 'Accessing magento'
echo -en "\033[0;33m"\
  && docker run --network cicd --rm curlimages/curl http://mage-nginx/magento_version/ \
  && echo -en "\033[0m"
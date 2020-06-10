#!/bin/bash

info () {
  echo -e "\033[0;34m" $* "\033[0m"
}

DIND_CONTAINER=$(docker run -d --privileged -p 12375:2375 -p 8877:80 -e DOCKER_TLS_CERTDIR="" docker:dind)
export DOCKER_HOST=tcp://localhost:12375

info 'Building Tool'
docker build -t tool ./tool
info 'Building PHP CLI'
docker build -t php-cli ./php-cli
info 'Building PHP FPM'
docker build -t mage-php-fpm ./php-fpm
info 'Building Nginx'
docker build -t magento ./nginx
info 'Building Mariadb'
docker build -t db ./db

info 'Create volume'
docker volume create --name mage

info 'Create network'
docker network create --driver bridge cicd

info 'Starting PHP-FPM'
PHP_CONTAINER=$(docker run --rm -d \
  -p 9000:9000 \
  --name fpm \
  --network cicd \
  -v mage:/themount\
  -w=/themount/magento-ce\
  mage-php-fpm)

info 'Starting MariaDB'
docker run --rm -d \
  -p 3306:3306 \
  --name db \
  --network cicd \
  -e MYSQL_ROOT_PASSWORD=secretpw \
  -e 'MYSQL_ROOT_HOST=%' \
  -e MYSQL_DATABASE=main \
  db

info 'Starting Nginx'
MAGENTO_CONTAINER=$(docker run --rm -d \
  --network cicd \
  --name magento \
  -v mage:/themount magento
)

info 'Running Tool Unit Tests'
docker run --rm --name tool tool self:run-tests

info 'Running Tool - Setup Magento'
docker run --rm \
  --name tool \
  --network cicd \
  --env-file .env \
  -v /var/run/docker.sock:/var/run/docker.sock \
  -v mage:/magento \
  tool \
  setup:install

info 'Running Tool - Verify Setup'
docker run --rm \
  --name tool \
  --network cicd \
  --env-file .env \
  -v /var/run/docker.sock:/var/run/docker.sock \
  -v mage:/magento \
  tool \
  setup:verify

info 'Running Tool - Run Test'
docker run --rm \
  --name tool \
  --network cicd \
  --env-file .env \
  -v /var/run/docker.sock:/var/run/docker.sock \
  -v mage:/magento \
  tool \
  test:run basic-workflow

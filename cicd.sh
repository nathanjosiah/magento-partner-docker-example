#!/bin/bash

info () {
  echo -e "\033[0;34m" $* "\033[0m"
}

DIND_CONTAINER=$(docker run -d --privileged -p 12375:2375 -p 8877:80 -e DOCKER_TLS_CERTDIR="" docker:dind)
export DOCKER_HOST=tcp://localhost:12375

info 'Building Tool'
docker build -t tool ./tool
info 'Pulling PHP images'
docker pull magento/magento-cloud-docker-php:7.3-cli-1.2
docker pull magento/magento-cloud-docker-php:7.3-fpm-1.2
docker pull magento/magento-cloud-docker-php:7.4-cli-1.2
docker pull magento/magento-cloud-docker-php:7.4-fpm-1.2

info 'Tagging (aliasing) Available PHP Versions'
docker image tag magento/magento-cloud-docker-php:7.3-cli-1.2 php73-cli
docker image tag magento/magento-cloud-docker-php:7.3-fpm-1.2 php73-fpm
docker image tag magento/magento-cloud-docker-php:7.4-cli-1.2 php74-cli
docker image tag magento/magento-cloud-docker-php:7.4-fpm-1.2 php74-fpm
info 'Building Nginx'
docker build -t magento ./nginx
info 'Building Mariadb'
docker build -t db ./db

info 'Create volume'
docker volume create --name mage

info 'Create network'
docker network create --driver bridge cicd

info 'Starting PHP-FPM 7.3'
docker run --rm -d \
  --name fpm-73 \
  --network cicd \
  -v mage:/magento\
  -w=/magento/magento-ce\
  php73-fpm
info 'Starting PHP-FPM 7.4'
docker run --rm -d \
  --name fpm-74 \
  --network cicd \
  -v mage:/magento\
  -w=/magento/magento-ce\
  php74-fpm

info 'Starting MariaDB'
docker run --rm -d \
  -p 3306:3306 \
  --name db \
  --network cicd \
  -e MYSQL_ROOT_PASSWORD=secretpw \
  -e 'MYSQL_ROOT_HOST=%' \
  -e MYSQL_DATABASE=main \
  db

#info 'Starting ElasticSearch 6'
#docker run --rm -d \
#  --name elasticsearch6 \
#  -e "discovery.type=single-node"\
#  elasticsearch:6.8.4
#
#info 'Starting ElasticSearch 7'
#docker run --rm -d \
#  --name elasticsearch7 \
#  -e "discovery.type=single-node"\
#  elasticsearch:7.1.1

info 'Starting Nginx'
docker run --rm -d \
  --network cicd \
  --name magento \
  -v mage:/magento magento


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
  setup:install --php 7.3

info 'Running Tool - Verify Setup for 7.3'
docker run --rm \
  --name tool \
  --network cicd \
  --env-file .env \
  -v /var/run/docker.sock:/var/run/docker.sock \
  -v mage:/magento \
  tool \
  setup:verify --php 7.3

info 'Pulling test php value from config.'
docker run --rm \
  --name tool \
  --network cicd \
  --env-file .env \
  -v /var/run/docker.sock:/var/run/docker.sock \
  -v mage:/magento \
  tool \
  test:config:get-php-version --config /app/etc/config.xml --name BasicUpgradeTest

info 'Switching php to 7.4'
docker exec -it magento sed -i 's/server fpm-73/server fpm-74/' /etc/nginx/conf.d/default.conf
docker exec -it magento nginx reload

info 'Running Tool - Verify Setup for 7.4'
docker run --rm \
  --name tool \
  --network cicd \
  --env-file .env \
  -v /var/run/docker.sock:/var/run/docker.sock \
  -v mage:/magento \
  tool \
  setup:verify --php 7.4

info 'Running Tool - Run Test'
docker run --rm \
  --name tool \
  --network cicd \
  --env-file .env \
  -v /var/run/docker.sock:/var/run/docker.sock \
  -v mage:/magento \
  tool \
  test:run --php 7.3 basic-workflow

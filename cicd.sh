#!/bin/bash

info () {
  echo -e "\033[0;34m" $* "\033[0m"
}

DIND_CONTAINER=$(docker run -d --privileged -p 12375:2375 -p 8877:80 -e DOCKER_TLS_CERTDIR="" docker:dind)
export DOCKER_HOST=tcp://localhost:12375

info 'Building Tool'
docker build -t tool ./tool
info 'Building Nginx'
docker build -t magento ./nginx
info 'Building Mariadb'
docker build -t db ./db

info 'Initializing environment'
docker run --rm \
  --name tool \
  --network cicd \
  --env-file .env \
  -v /var/run/docker.sock:/var/run/docker.sock \
  -v mage:/magento \
  tool \
  env:build

#info 'Pulling test php value from config.'
#PHP_VERSION=$(docker run --rm \
#  --name tool \
#  --network cicd \
#  --env-file .env \
#  -v /var/run/docker.sock:/var/run/docker.sock \
#  -v mage:/magento \
#  tool \
#  test:config:get-php-version --config /app/etc/config.xml --name BasicUpgradeTest)
#
#echo Got version $PHP_VERSION for BasicUpgradeTest

info 'Running Tool Unit Tests'
docker run --rm --name tool tool self:run-tests

info 'Running BasicUpgradeTest'
docker run --rm \
  -t \
 --name tool \
 --network cicd \
 --env-file .env \
 -v /var/run/docker.sock:/var/run/docker.sock \
 -v mage:/magento \
 tool \
 test:run BasicUpgradeTest

#info 'Running Tool - Setup Magento for ' $PHP_VERSION
#docker run --rm \
#  --name tool \
#  --network cicd \
#  --env-file .env \
#  -v /var/run/docker.sock:/var/run/docker.sock \
#  -v mage:/magento \
#  tool \
#  setup:install --php $PHP_VERSION
#
#info 'Running Tool - Verify Setup for' $PHP_VERSION
#docker run --rm \
#  --name tool \
#  --network cicd \
#  --env-file .env \
#  -v /var/run/docker.sock:/var/run/docker.sock \
#  -v mage:/magento \
#  tool \
#  setup:verify --php $PHP_VERSION
#
#info 'Running Tool - Run MFTF Test for '$PHP_VERSION
#docker run --rm \
# --name tool \
# --network cicd \
# --env-file .env \
# -v /var/run/docker.sock:/var/run/docker.sock \
# -v mage:/magento \
# tool \
# test:run --php $PHP_VERSION basic-workflow
#
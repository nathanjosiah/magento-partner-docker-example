#!/bin/bash
command=$1
version=$2

docker run --rm \
  --network cicd\
  -e COMPOSER_HOME=/magento/.composer\
  -v mage:/magento \
  --link magento-php$(echo -n $service | sed 's/\.//'):magento \
  magento/magento-cloud-docker-php:${version}-cli-1.2 \
  $command
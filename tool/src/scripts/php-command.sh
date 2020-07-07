#!/bin/bash
command=$1
version=$2

toExecute="docker run --rm \
  --network cicd\
  -e COMPOSER_HOME=/magento/.composer\
  -v mage:/magento \
  -v /artifacts:/artifacts \
  --link magento-php$(echo -n $version | sed 's/\.//'):magento \
  magento/magento-cloud-docker-php:${version}-cli-1.2 \
  $command"
echo '[script] - '$toExecute
eval "$toExecute"
#!/bin/bash

echo 'Building Tool'
docker build -t tool ./tool
echo 'Building PHP'
docker build -t myphp ./php
echo 'Building magento'
docker build -t magento ./magento

if [[ ! -d magento-volume ]]; then
  echo 'Creating volume directory'
  mkdir magento-volume
fi

echo 'Maybe create volume'
docker volume create \
  --name mage \
  --opt type=none \
  --opt device=$(pwd)/magento-volume \
  --opt o=bind



echo 'Running Magento'
MAGENTO_CONTAINER=$(docker run --rm -d -p 8765:80 -v mage:/usr/share/nginx/html magento)
trap "docker stop $MAGENTO_CONTAINER" EXIT

echo 'Running Tool'
docker run --rm \
  -v /var/run/docker.sock:/var/run/docker.sock \
  -v mage:/magento \
  tool \
  somearg

echo 'Accessing "configured magento" loaded from github'
curl http://localhost:8765/magento-ce/
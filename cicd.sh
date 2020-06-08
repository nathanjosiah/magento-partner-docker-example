#!/bin/bash

info () {
  echo -e "\033[0;34m" $* "\033[0m"
}

DIND_CONTAINER=$(docker run -d --rm --privileged -p 12375:2375 -p 8877:80 -e DOCKER_TLS_CERTDIR="" docker:dind)
export DOCKER_HOST=tcp://localhost:12375

info 'Building Tool'
docker build -t tool ./tool
info 'Building PHP'
docker build -t myphp ./php
info 'Building magento'
docker build -t magento ./magento

info 'Create volume'
docker volume create \
  --name mage

info 'Running Magento'
MAGENTO_CONTAINER=$(docker run --rm -d \
  -p 80:80 \
  --name magento \
  -v mage:/usr/share/nginx/html magento
)

info 'Running Tool'
docker run --rm \
  --name tool \
  -v /var/run/docker.sock:/var/run/docker.sock \
  -v mage:/magento \
  tool \
  somearg

info 'Accessing "configured magento" loaded from github'
echo -en "\033[0;33m"\
  && curl http://localhost:8877/magento-ce/ \
  && echo -en "\033[0m"

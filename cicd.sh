#!/bin/bash

info () {
  echo -e "\033[0;34m" $* "\033[0m"
}

if [[ ! -f .env ]]; then
  info "Didn't find an .env file. Assuming environment variables from host environment"
  env | grep MAGE_ > .env
fi

DIND_CONTAINER=$(docker run -d --privileged -p 12375:2375 -p 4444:4444 -p 5900:5900 -e DOCKER_TLS_CERTDIR="" docker:dind)
export DOCKER_HOST=tcp://localhost:12375

info 'Creating volume'
docker volume create --name mage

info 'Initializing network'
docker network create --driver bridge cicd

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

info 'Running Tool Unit Tests'
docker run --rm --name tool tool self:run-tests

info 'Running BasicUpgradeTest'
docker run --rm \
 --name tool \
 --network cicd \
 --env-file .env \
 -v /var/run/docker.sock:/var/run/docker.sock \
 -v mage:/magento \
 tool \
 test:run BasicUpgradeTest

# For future use: Example of running another test in parrallel.
# Each DinD container for this system represents a test environment.
# The DinD containers can be all started together at the top and then specific tool test:run's can be run within each dind container in a separate process.
# They will be fully isolated since the tests can interact with any combination of services and version at a given time
#
# DIND_CONTAINER=$(docker run -d --privileged -p 12376:2375 -e DOCKER_TLS_CERTDIR="" docker:dind)
# export DOCKER_HOST=tcp://localhost:12376
# docker run --rm \
# --name tool \
# --network cicd \
# --env-file .env \
# -v /var/run/docker.sock:/var/run/docker.sock \
# -v mage:/magento \
# tool \
# test:run SomeOtherTest
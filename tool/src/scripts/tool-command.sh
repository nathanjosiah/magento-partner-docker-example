#!/bin/bash
command=$1

docker run --rm \
   --network cicd \
   --env-file <(env | grep MAGE_) \
   -v /var/run/docker.sock:/var/run/docker.sock \
   -v mage:/magento \
   tool $command
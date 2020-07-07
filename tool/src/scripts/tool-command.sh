#!/bin/bash
command=$1

toExecute="docker run --rm \
   --network cicd \
   --env-file <(env | grep MAGE_) \
   -v /artifacts:/artifacts \
   -v /var/run/docker.sock:/var/run/docker.sock \
   -v mage:/magento \
   tool $command"

echo '[script] - ' $toExecute
eval "$toExecute"
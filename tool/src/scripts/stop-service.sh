#!/bin/bash
service=$1
version=$2

if [[ "$service" = "selenium" ]]; then
  docker stop selenium
fi
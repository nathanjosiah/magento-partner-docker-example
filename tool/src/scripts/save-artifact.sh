#!/bin/bash
path=$1
name=$2
purge=$3

echo '[script] - Saving artifact directory from source' $path to $MAGE_ARTIFACTS_DIRECTORY/$name
mkdir -p $MAGE_ARTIFACTS_DIRECTORY/$name
cp -R $path/* $MAGE_ARTIFACTS_DIRECTORY/$name

if [[ -n "$purge" ]]; then
  echo '[script] - Purging source artifact directory contents' $path
  rm -rf $path/*
fi

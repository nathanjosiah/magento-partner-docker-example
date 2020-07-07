#!/bin/bash
path=$1
name=$2
purge=$3

mkdir -p $ARTIFACT_DIRECTORY/$name
cp -R $path/* $ARTIFACT_DIRECTORY/$name
echo '[script] - Saving artifact directory from source' $path

if [[ -n "$purge" ]]; then
  echo '[script] - Purging source artifact directory contents' $path
  rm -rf $path/*
fi

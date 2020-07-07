#!/bin/bash
sql=$1

toExecute="docker exec db mysql -uroot --password=secretpw main -e '$sql'"

echo '[script] - ' $toExecute
eval "$toExecute"
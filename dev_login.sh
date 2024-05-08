#!/bin/bash
# Taken from https://stackoverflow.com/a/53737133
container_id=$(docker ps|grep api-php|cut -d' ' -f1)
echo $container_id
cmd="docker exec -u 0 -it "$container_id" /bin/bash"
echo $cmd
exec $cmd
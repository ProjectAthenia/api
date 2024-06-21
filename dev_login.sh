#!/bin/bash
# Taken from https://stackoverflow.com/a/53737133
source .env
container_id=$(docker ps --filter network=$NETWORK|grep php|cut -d' ' -f1)
echo $container_id
cmd="docker exec -u 0 -it "$container_id" /bin/bash"
echo $cmd
exec $cmd
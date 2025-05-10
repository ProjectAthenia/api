#!/bin/bash
# Script to execute a command inside the Docker container
source .env
container_id=$(docker ps --filter network=$NETWORK|grep docker-php-entry|cut -d' ' -f1)

if [ -z "$container_id" ]; then
    echo "Error: No running container found"
    exit 1
fi

if [ $# -eq 0 ]; then
    echo "Error: No command provided"
    echo "Usage: $0 <command>"
    exit 1
fi

# Execute the command inside the container
docker exec -u 0 "$container_id" "$@" 
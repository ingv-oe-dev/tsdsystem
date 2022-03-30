#!/bin/bash

set -e

cat > ./configs/db.json << EOF
{
    "host": "${DB_HOST}",
    "user": "${DB_USER}",
    "pwd": "${DB_PASSWORD}",
    "db": "${DB_NAME}",
    "port": "${DB_PORT}"
}
EOF

exec $@

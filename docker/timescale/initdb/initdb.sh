#!/bin/sh
set -e

# Set default user password
PGPASSWORD=${DB_PASSWORD}
echo "Executing initialization script ..."

echo "RUN: Create user $TSD_DB_USER"
cat /sql_create/role.sql \
  | sed "s|\TSD_DB_USER|$TSD_DB_USER|g; s|\TSD_DB_PASSWORD|$TSD_DB_PASSWORD|g" \
  | psql -a -b -v ON_ERROR_STOP=1 "$POSTGRES_USER" "$DB_NAME"

echo "RUN: Initialize TSDSystem DB $TSD_DB"
cat /sql_create/init.sql \
  | sed "s|\bTSD_DB\b|$TSD_DB|g; s|\TSD_DB_USER|$TSD_DB_USER|g; s|\TSD_DB_PASSWORD|$TSD_DB_PASSWORD|g" \
  | psql -a -b -v ON_ERROR_STOP=1 "$POSTGRES_USER" "$DB_NAME"

# Set user password
PGPASSWORD=${TSD_DB_PASSWORD}

echo "RUN: Create TSDSystem DB main schema"
psql ${TSD_DB} ${TSD_DB_USER} < /sql_create/tsd_main.sql

echo "RUN: Create TSDSystem DB pnet schema"
psql ${TSD_DB} ${TSD_DB_USER} < /sql_create/tsd_pnet.sql

echo "RUN: Create TSDSystem DB users schema"
psql ${TSD_DB} ${TSD_DB_USER} < /sql_create/tsd_users.sql

echo "RUN: Update TSDSystem DB public schema"
psql ${TSD_DB} ${TSD_DB_USER} < /sql_create/public.sql

echo "Initialization script finished."

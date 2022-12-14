#!/bin/sh

PGPASSWORD=${DB_PASSWORD}
echo "Executing scripts from directory initdb"

echo "RUN create_tsdsystem_role"
sed -i "s/\bTSD_DB_USER\b/$TSD_DB_USER/g" /sql_create/role.sql
sed -i "s/\bTSD_DB_PASSWORD\b/$TSD_DB_PASSWORD/g" /sql_create/role.sql
psql -h ${DB_HOST} -U ${DB_USER} < /sql_create/role.sql
echo "END create_tsdsystem_role"

echo "RUN init.sql"
sed -i "s/CREATE DATABASE .*/CREATE DATABASE $TSD_DB/g" /sql_create/init.sql
sed -i "s/connect .*/connect $TSD_DB/g" /sql_create/init.sql
sed -i "s/\bTSD_DB_USER\b/$TSD_DB_USER/g" /sql_create/init.sql
psql -h ${DB_HOST} -U ${DB_USER} < /sql_create/init.sql
echo "END init.sql"

# Set TSD User password
PGPASSWORD=${TSD_DB_PASSWORD}

echo "RUN tsd_main.sql"
psql -h ${DB_HOST} -U ${TSD_DB_USER} -d ${TSD_DB} < /sql_tsd/tsd_main.sql
echo "END tsd_main.sql"

echo "RUN tsd_pnet.sql"
psql -h ${DB_HOST} -U ${TSD_DB_USER} -d ${TSD_DB} < /sql_tsd/tsd_pnet.sql
echo "END tsd_pnet.sql"

echo "RUN tsd_users.sql"
psql -h ${DB_HOST} -U ${TSD_DB_USER} -d ${TSD_DB} < /sql_tsd/tsd_users.sql
echo "END tsd_users.sql"

echo "RUN public.sql"
psql -h ${DB_HOST} -U ${TSD_DB_USER} -d ${TSD_DB}  < /sql_tsd/public.sql
echo "END public.sql"

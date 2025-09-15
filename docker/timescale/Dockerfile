FROM timescale/timescaledb-ha:pg14.2-ts2.6.0-oss-latest

# Copy init scripts into the image
COPY sql/ /sql_create/
COPY initdb/initdb.sh /docker-entrypoint-initdb.d/01-initdb.sh

FROM timescale/timescaledb-ha:pg14.2-ts2.6.0-oss-latest

LABEL Maintainer="Fabrizio Pistagna <fabrizio.pistagna@ingv.it>, Mario Torrisi <mario.torrisi@ingv.it>"

COPY sql/init.sql /docker-entrypoint-initdb.d/01_init_schema.sql

COPY sql/tsd_main.sql /docker-entrypoint-initdb.d/02_tsd_main.sql

COPY sql/tsd_pnet.sql /docker-entrypoint-initdb.d/03_tsd_pnet.sql

COPY sql/tsd_users.sql /docker-entrypoint-initdb.d/04_tsd_users.sql

COPY sql/public.sql /docker-entrypoint-initdb.d/05_public.sql
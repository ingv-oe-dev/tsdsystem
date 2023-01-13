# TSDSystem

This manual is intended for self complete installation + initialization using Docker.

## Docker Installation

Use the following guides (to run for example on a Debian) to install `docker` + `docker compose`:
- https://docs.docker.com/engine/install/debian/
- https://docs.docker.com/compose/install/linux/#install-the-plugin-manually


## Download repository
Download `tsdsystem` repository from GitHub: `git clone https://github.com/ingv-oe-dev/tsdsystem.git`

It will download the repository into `tsdsystem` directory.

## Environment variables

Set the environment variables into file `.env`:

Database PostGreSQL:
- `DB_HOST`=db
- `DB_PORT`=5432
- `DB_USER` (string)
- `DB_PASSWORD` (string)

Timescale DB extension (TSDSystem):
- `TSD_DB` (string)
- `TSD_DB_USER` (string)
- `TSD_DB_PASSWORD` (string)

PGAdmin:
- `PGADMIN_EMAIL` (string)
- `PGADMIN_PASSWORD` (string)

Secret key to generate/decode JWT tokens:
- `SERVER_KEY` (string)

To enable requests originated from other websites:
- `APP_ALLOWED_HOSTS` (string - ex. all: '*' or a list of addresses: '127.0.0.1, 192.168.1.1, etc.')
- 
Admin (super-user) settings:
- `ADMIN_ID` (administrator user identifier - choose any integer <= 0)
- `ADMIN_EMAIL` (used also as recipient for email communication - i.e. on members registration)
- `ADMIN_PASSWORD` (coupled with `ADMIN_EMAIL` to access the web interface as administrator)

To control debug level:
- `ENV` ('*development*' or '*production*')

### Optional variables

Spatial information:
- `EPSG_DEGREE` default projection using degrees - if not specified it will use WGS84 (SRID)
- `EPSG_M` default projection using meters - if not specified it will use UTM zone 32N (SRID)

SMTP settings (for emails sending):
- `SMTP_HOST`
- `SMTP_USERNAME`
- `SMTP_PASSWORD`
- `SMTP_AUTH`
- `SMTP_SECURE`

Regexp pattern for users' email registration:
- `REG_PATTERN` (PCRE2 [PHP >=7.3] - if not set, it allows any string)

Public URL:
- `PUBLIC_URL`

## TSDSystem initialization

Run:
- `docker compose up -d`

Init PostgreSQL database:
- `docker compose -f docker-compose.initdb.yml up -d`

### Full installation notes

A full installation using `docker-compose.full.yml` is recommended to expose the service on Internet. To expose on Internet, the installation server needs the certificate files into the `nginx` directory, called:
- server.key
- server.crt
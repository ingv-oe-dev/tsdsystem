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

Into the `tsdsystem` directory, set the environment variables into file `.env`:

Database PostGreSQL:
- `DB_HOST`=db
- `DB_PORT`=5432
- `DB_USER` (string)
- `DB_PASSWORD` (string)

Timescale DB extension (the engine used for timeseries storage):
- `TSD_DB` (string)
- `TSD_DB_USER` (string)
- `TSD_DB_PASSWORD` (string)

PGAdmin (if you choose a [full installation](#full-installation-notes)):
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

## TSDSystem installation

### Basic installation
For a basic installation run the following command:
- `docker compose up -d`

The web service will respond on port `8000`.

### Full installation

A full installation using `docker-compose.full.yml` is recommended to expose the service on Internet (default port `80/443` respectively for `http/https`). To expose on Internet, the installation server needs the certificate files into the `tsdsystem/docker/nginx` directory, called:
- server.key
- server.crt
>**Tip**: For initial testing, administrator can generate a **self-signed SSL certificate** as follows:
>#### *Step 1*: Generate a Private Key
>```shell
>openssl genrsa -out server.key 2048
>```
>#### *Step 2*: Generate a CSR (Certificate Signing Request)
>```shell
>openssl req -new -key server.key -out server.csr
>```
>#### *Step 3*: Generating a Self-Signed Certificate
>```shell
>openssl x509 -req -in server.csr -signkey server.key -out server.crt -days 3650 >-sha256
>```
>At this point you have the desired couple of files needed to continue the installation.

For a full installation run:
- `docker compose -f docker-compose.full.yml up -d`

>**Note:** It will install a local instance of [PGAdmin](https://www.pgadmin.org/) tool, responding on port `5050/5051` respectively for `http/https` protocols.

>**Hint:** It is also available the installation of a local instance of [Grafana](https://grafana.com/) tool, responding on port `3000`, by uncommenting lines in files:
>- `docker-compose.full.yml` (`services:grafana` and `volumes:grafana-storage` sections);
>- `docker-services.yml` (`services:grafana` and `services:renderer` sections).

## Database PostgreSQL initialization

Regardless the choice of a [Basic](#basic) or [Full](#full) installation, the correct start up of the service requires the initialization of the PostgreSQL database structure by running the command:
- `docker compose -f docker-compose.initdb.yml up -d`
# TSDSystem

This manual is intended for self complete installation + initialization using Docker.

## Docker installation

Use the following guides (to run for example on a Debian) to install `docker` + `docker compose`:
- https://docs.docker.com/engine/install/debian/
- https://docs.docker.com/compose/install/linux/#install-the-plugin-manually

After installing docker, it is recommended to add your system user to the group `docker` to manage Docker as a non-root user.

*"The Docker daemon binds to a Unix socket, not a TCP port. By default it’s the root user that owns the Unix socket, **and other users can only access it using `sudo`**. **The Docker daemon always runs as the root user**. If you don’t want to preface the docker command with sudo, create a Unix group called docker and add users to it. When the Docker daemon starts, it creates a Unix socket accessible by members of the docker group. On some Linux distributions, the system automatically creates this group when installing Docker Engine using a package manager. In that case, there is no need for you to manually create the group."* [**Reference**: https://docs.docker.com/engine/install/linux-postinstall/]


Since group `docker` will be created automatically, usually you only need to run this command:
```shell
sudo usermod -aG docker $USER
```
where `$USER` refers to your system user.

For a complete guide about this argument refer to: https://docs.docker.com/engine/install/linux-postinstall/

## Download repository
Download `tsdsystem` repository from GitHub: `git clone https://github.com/ingv-oe-dev/tsdsystem.git`

It will download the repository into `tsdsystem` directory.

## Setting environment variables

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

Secret key to generate/decode JWT tokens:
- `SERVER_KEY` (string)
  
Admin (super-user) settings:
- `ADMIN_ID` (administrator user identifier - choose any integer <= 0)
- `ADMIN_EMAIL` (used also as recipient for email communication - i.e. on members registration)
- `ADMIN_PASSWORD` (coupled with `ADMIN_EMAIL` to access the web interface as administrator)

To enable requests originated from other websites:
- `APP_ALLOWED_HOSTS` (string - ex. all: '*' or a list of addresses: '127.0.0.1, 192.168.1.1, etc.')

PGAdmin (if you choose a [full installation](#full-installation-notes)):
- `PGADMIN_EMAIL` (string)
- `PGADMIN_PASSWORD` (string)

To control debug level:
- `ENV` ('*development*' or '*production*')

### Optional variables

Spatial information:
- `EPSG_DEGREE` default projection using degrees - if not specified it will use WGS84 (SRID)
- `EPSG_M` default projection using meters - if not specified it will use UTM zone 32N (SRID)

SMTP settings (for emails sending, e.g. on users registration):
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

The repository contains two building files:
- `docker-compose.yml`
- `docker-compose.full.yml`

Respectively for the [Basic](#basic) and the [Full](#full) installation.


### Basic installation
The basic installation build up all you need to create a TSDSystem instance. For a basic installation run the following command:
- `docker compose up -d`

The web service will respond on port `8000`.

### Full installation

A full installation using `docker-compose.full.yml` is recommended to expose the service on Internet (default port `80/443` respectively for `http/https`). To expose on Internet, the installation server needs the certificate files into the `tsdsystem/docker/nginx` directory, called:
- `server.key`
- `server.crt`
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
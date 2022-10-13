--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- Database: tsdsystem

-- DROP DATABASE IF EXISTS tsdsystem;

CREATE DATABASE tsdsystem
    WITH 
    OWNER = superuser
    ENCODING = 'UTF8'
    LC_COLLATE = 'Italian_Italy.1252'
    LC_CTYPE = 'Italian_Italy.1252'
    TABLESPACE = pg_default
    CONNECTION LIMIT = -1;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- EXTENSION: timescaledb

CREATE EXTENSION IF NOT EXISTS timescaledb CASCADE;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- EXTENSION: postgis

CREATE EXTENSION IF NOT EXISTS postgis CASCADE;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- EXTENSION: uuid-ossp

CREATE EXTENSION IF NOT EXISTS "uuid-ossp" CASCADE;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- SCHEMA: tsd_pnet

CREATE SCHEMA IF NOT EXISTS tsd_pnet
    AUTHORIZATION superuser;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- SCHEMA: tsd_main

CREATE SCHEMA IF NOT EXISTS tsd_main
    AUTHORIZATION superuser;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- SCHEMA: tsd_users

CREATE SCHEMA IF NOT EXISTS tsd_users
    AUTHORIZATION superuser;
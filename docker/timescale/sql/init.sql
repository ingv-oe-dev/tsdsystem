--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- Database: tsdsystem

-- DROP DATABASE IF EXISTS tsdsystem;

CREATE DATABASE tsdsystem
    WITH 
    OWNER = tsdsystem
    ENCODING = 'UTF8'
    LC_COLLATE = 'C.UTF-8'
    LC_CTYPE = 'C.UTF-8'
    TABLESPACE = pg_default
    CONNECTION LIMIT = -1;

\connect tsdsystem;
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
    AUTHORIZATION tsdsystem;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- SCHEMA: tsd_main

CREATE SCHEMA IF NOT EXISTS tsd_main
    AUTHORIZATION tsdsystem;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- SCHEMA: tsd_users

CREATE SCHEMA IF NOT EXISTS tsd_users
    AUTHORIZATION tsdsystem;
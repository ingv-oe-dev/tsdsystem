--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- Table: tsd_pnet.owners

-- DROP TABLE IF EXISTS tsd_pnet.owners;
CREATE TABLE IF NOT EXISTS tsd_pnet.owners
(
    id SERIAL NOT NULL,
    name character varying(255) COLLATE pg_catalog."default" NOT NULL,
	create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer,
    CONSTRAINT owners_pkey PRIMARY KEY (id)
);

CREATE UNIQUE INDEX tsd_pnet_owners_lower_name_idx ON tsd_pnet.owners (LOWER(name))

TABLESPACE pg_default;

ALTER TABLE IF EXISTS tsd_pnet.owners
    OWNER to postgres;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- Table: tsd_pnet.nets

-- DROP TABLE IF EXISTS tsd_pnet.nets;

CREATE TABLE IF NOT EXISTS tsd_pnet.nets
(
    id SERIAL NOT NULL,
    name character varying(255) COLLATE pg_catalog."default" NOT NULL,
    owner_id integer,
	create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer,
    CONSTRAINT nets_pkey PRIMARY KEY (id)
);

CREATE UNIQUE INDEX tsd_pnet_nets_lower_name_idx ON tsd_pnet.nets (LOWER(name))

TABLESPACE pg_default;

ALTER TABLE IF EXISTS tsd_pnet.nets
    OWNER to postgres;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- Table: tsd_pnet.sensortypes

-- DROP TABLE IF EXISTS tsd_pnet.sensortypes;

CREATE TABLE IF NOT EXISTS tsd_pnet.sensortypes
(
    id SERIAL NOT NULL,
    name character varying(255) COLLATE pg_catalog."default" NOT NULL,
	json_schema jsonb,
	create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer,
    CONSTRAINT sensortypes_pkey PRIMARY KEY (id)
);

CREATE UNIQUE INDEX tsd_pnet_sensortypes_lower_name_idx ON tsd_pnet.sensortypes (LOWER(name))

TABLESPACE pg_default;

ALTER TABLE IF EXISTS tsd_pnet.sensortypes
    OWNER to postgres;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- Table: tsd_pnet.sites

-- DROP TABLE IF EXISTS tsd_pnet.sites;

CREATE TABLE IF NOT EXISTS tsd_pnet.sites
(
    id SERIAL NOT NULL,
    name character varying(255) COLLATE pg_catalog."default" NOT NULL,
	coords geometry,
    quote real,
	info jsonb,
	create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer,
    CONSTRAINT sites_pkey PRIMARY KEY (id)
);

CREATE UNIQUE INDEX tsd_pnet_sites_lower_name_idx ON tsd_pnet.sites (LOWER(name))

TABLESPACE pg_default;

ALTER TABLE IF EXISTS tsd_pnet.sites
    OWNER to postgres;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- Table: tsd_pnet.sensors

-- DROP TABLE IF EXISTS tsd_pnet.sensors;

CREATE TABLE IF NOT EXISTS tsd_pnet.sensors
(
    id SERIAL NOT NULL,
    name character varying(255) COLLATE pg_catalog."default" NOT NULL,
	coords geometry,
    quote real,
	metadata jsonb,
	custom_props jsonb,
	sensortype_id integer,
	net_id integer,
    site_id integer,
    create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer,
    CONSTRAINT sensors_pkey PRIMARY KEY (id)
);

CREATE UNIQUE INDEX tsd_pnet_sensors_lower_name_idx ON tsd_pnet.sensors (LOWER(name))

TABLESPACE pg_default;

ALTER TABLE IF EXISTS tsd_pnet.sensors
    OWNER to postgres;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- Table: tsd_pnet.channels

-- DROP TABLE IF EXISTS tsd_pnet.channels;

CREATE TABLE IF NOT EXISTS tsd_pnet.channels
(
    id SERIAL NOT NULL,
    name character varying(255) COLLATE pg_catalog."default" NOT NULL,
    sensor_id integer,
    info jsonb,
	create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer,
    CONSTRAINT channels_pkey PRIMARY KEY (id)
);

CREATE UNIQUE INDEX tsd_pnet_channels_lower_name_sensor_id_idx ON tsd_pnet.channels (LOWER(name), sensor_id)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS tsd_pnet.channels
    OWNER to postgres;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
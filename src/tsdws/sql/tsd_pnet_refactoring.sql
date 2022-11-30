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
    additional_info jsonb,
	create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer,
    CONSTRAINT nets_pkey PRIMARY KEY (id)
);

TABLESPACE pg_default;

ALTER TABLE IF EXISTS tsd_pnet.nets
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
	additional_info jsonb,
	create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer,
    CONSTRAINT sites_pkey PRIMARY KEY (id)
);

TABLESPACE pg_default;

ALTER TABLE IF EXISTS tsd_pnet.sites
    OWNER to postgres;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- Table: tsd_pnet.stations

-- DROP TABLE IF EXISTS tsd_pnet.stations;

CREATE TABLE IF NOT EXISTS tsd_pnet.stations
(
    id SERIAL NOT NULL,
    name character varying(255) COLLATE pg_catalog."default" NOT NULL,
	coords geometry,
    quote real,
    site_id integer,
    net_id integer,
	additional_info jsonb,
	create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer,
    CONSTRAINT stations_pkey PRIMARY KEY (id)
);

TABLESPACE pg_default;

ALTER TABLE IF EXISTS tsd_pnet.stations
    OWNER to postgres;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- Table: tsd_pnet.sensortype_categories

-- DROP TABLE IF EXISTS tsd_pnet.sensortype_categories;

CREATE TABLE IF NOT EXISTS tsd_pnet.sensortype_categories
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
    CONSTRAINT sensortype_categories_pkey PRIMARY KEY (id)
);

TABLESPACE pg_default;

ALTER TABLE IF EXISTS tsd_pnet.sensortype_categories
    OWNER to postgres;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- Table: tsd_pnet.sensortypes

-- DROP TABLE IF EXISTS tsd_pnet.sensortypes;

CREATE TABLE IF NOT EXISTS tsd_pnet.sensortypes
(
    id SERIAL NOT NULL,
    name character varying(255) COLLATE pg_catalog."default" NOT NULL,
    model character varying(255) COLLATE pg_catalog."default",
    n_components integer,
    sensortype_category_id integer,
    response_parameters jsonb,
	additional_info jsonb,
	create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer,
    CONSTRAINT sensortypes_pkey PRIMARY KEY (id)
);

TABLESPACE pg_default;

ALTER TABLE IF EXISTS tsd_pnet.sensortypes
    OWNER to postgres;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- Table: tsd_pnet.sensors

-- DROP TABLE IF EXISTS tsd_pnet.sensors;

CREATE TABLE IF NOT EXISTS tsd_pnet.sensors
(
    id SERIAL NOT NULL,
    name character varying(255) COLLATE pg_catalog."default" NOT NULL,
	serial_number character varying(255) COLLATE pg_catalog."default",
    station_id integer,
    sensortype_id integer,
    start_datetime timestamp without time zone,
    end_datetime timestamp without time zone,
	additional_info jsonb,
    create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer,
    CONSTRAINT sensors_pkey PRIMARY KEY (id)
);

TABLESPACE pg_default;

ALTER TABLE IF EXISTS tsd_pnet.sensors
    OWNER to postgres;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- Table: tsd_pnet.digitizertypes

-- DROP TABLE IF EXISTS tsd_pnet.digitizertypes;

CREATE TABLE IF NOT EXISTS tsd_pnet.digitizertypes
(
    id SERIAL NOT NULL,
    name character varying(255) COLLATE pg_catalog."default" NOT NULL,
	model character varying(255) COLLATE pg_catalog."default",
    final_sample_rate real,
    final_sample_rate_measure_unit real,
	sensitivity real,
    sensitivity_measure_unit real,
    dynamical_range real,
    dynamical_range_measure_unit real,
    additional_info jsonb,
    create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer,
    CONSTRAINT digitizertypes_pkey PRIMARY KEY (id)
);

TABLESPACE pg_default;

ALTER TABLE IF EXISTS tsd_pnet.digitizertypes
    OWNER to postgres;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- Table: tsd_pnet.digitizers

-- DROP TABLE IF EXISTS tsd_pnet.digitizers;

CREATE TABLE IF NOT EXISTS tsd_pnet.digitizers
(
    id SERIAL NOT NULL,
    name character varying(255) COLLATE pg_catalog."default" NOT NULL,
	serial_number character varying(255) COLLATE pg_catalog."default",
    digitizertype_id integer,
    start_datetime timestamp without time zone,
    end_datetime timestamp without time zone,
	additional_info jsonb,
    create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer,
    CONSTRAINT digitizers_pkey PRIMARY KEY (id)
);

TABLESPACE pg_default;

ALTER TABLE IF EXISTS tsd_pnet.digitizers
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
    digitizer_id integer,
    additional_info jsonb,
    create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer,
    CONSTRAINT channels_pkey PRIMARY KEY (id)
);

TABLESPACE pg_default;

ALTER TABLE IF EXISTS tsd_pnet.channels
    OWNER to postgres;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
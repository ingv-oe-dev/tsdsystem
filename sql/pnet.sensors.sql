-- Table: tsd_pnet.sensors

-- DROP TABLE IF EXISTS tsd_pnet.sensors;

CREATE TABLE IF NOT EXISTS tsd_pnet.sensors
(
    id SERIAL NOT NULL,
    name character varying(255) COLLATE pg_catalog."default" NOT NULL,
	coords geometry,
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
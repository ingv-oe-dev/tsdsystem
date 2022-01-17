-- Table: pnet.sensors

-- DROP TABLE IF EXISTS pnet.sensors;

CREATE TABLE IF NOT EXISTS pnet.sensors
(
    id SERIAL NOT NULL,
    name character varying(255) COLLATE pg_catalog."default" NOT NULL,
	coords geometry,
	metadata jsonb,
	custom_props jsonb,
	sensortype_id integer,
	net_id integer,
    create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer,
    CONSTRAINT sensors_pkey PRIMARY KEY (id),
	CONSTRAINT sensors_name_key UNIQUE (name)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS pnet.sensors
    OWNER to postgres;
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
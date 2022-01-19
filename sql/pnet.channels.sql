-- Table: pnet.channels

-- DROP TABLE IF EXISTS pnet.channels;

CREATE TABLE IF NOT EXISTS pnet.channels
(
    id SERIAL NOT NULL,
    name character varying(255) COLLATE pg_catalog."default" NOT NULL,
    sensor_id integer,
	create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer,
    CONSTRAINT channels_pkey PRIMARY KEY (id)
);

CREATE UNIQUE INDEX pnet_channels_lower_name_sensor_id_idx ON pnet.channels (LOWER(name), sensor_id);

TABLESPACE pg_default;

ALTER TABLE IF EXISTS pnet.channels
    OWNER to postgres;
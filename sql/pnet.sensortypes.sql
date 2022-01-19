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
-- Table: pnet.sensortypes

-- DROP TABLE IF EXISTS pnet.sensortypes;

CREATE TABLE IF NOT EXISTS pnet.sensortypes
(
    id SERIAL NOT NULL,
    name character varying(255) COLLATE pg_catalog."default" NOT NULL,
	default_props jsonb,
	create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer,
    CONSTRAINT sensortypes_pkey PRIMARY KEY (id),
	CONSTRAINT sensortypes_name_key UNIQUE (name)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS pnet.sensortypes
    OWNER to postgres;
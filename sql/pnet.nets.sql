-- Table: pnet.nets

-- DROP TABLE IF EXISTS pnet.nets;

CREATE TABLE IF NOT EXISTS pnet.nets
(
    id SERIAL NOT NULL,
    name character varying(255) COLLATE pg_catalog."default" NOT NULL,
	create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer,
    CONSTRAINT nets_pkey PRIMARY KEY (id)
);

CREATE UNIQUE INDEX pnet_nets_lower_name_idx ON pnet.nets (LOWER(name))

TABLESPACE pg_default;

ALTER TABLE IF EXISTS pnet.nets
    OWNER to postgres;
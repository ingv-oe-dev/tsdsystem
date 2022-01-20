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
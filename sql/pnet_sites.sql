-- Table: tsd_pnet.sites

-- DROP TABLE IF EXISTS tsd_pnet.sites;

CREATE TABLE IF NOT EXISTS tsd_pnet.sites
(
    id SERIAL NOT NULL,
    name character varying(255) COLLATE pg_catalog."default" NOT NULL,
	coords geometry,
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
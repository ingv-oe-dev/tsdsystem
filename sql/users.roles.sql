-- Table: tsd_users.roles

-- DROP TABLE IF EXISTS tsd_users.roles;

CREATE TABLE IF NOT EXISTS tsd_users.roles
(
    id SERIAL NOT NULL,
    name character varying(255) COLLATE pg_catalog."default" NOT NULL,
	description text,
	create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    CONSTRAINT roles_pkey PRIMARY KEY (id)
);
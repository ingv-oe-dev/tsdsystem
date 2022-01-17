-- Table: users.members

-- DROP TABLE IF EXISTS users.members;

CREATE TABLE IF NOT EXISTS users.members
(
    id SERIAL NOT NULL,
    email character varying(255) COLLATE pg_catalog."default" NOT NULL,
    password character(128) COLLATE pg_catalog."default" DEFAULT NULL::bpchar,
    salt character(128) COLLATE pg_catalog."default" DEFAULT NULL::bpchar,
    deleted timestamp without time zone,
    registered timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    confirmed timestamp without time zone,
    CONSTRAINT members_pkey PRIMARY KEY (id),
    CONSTRAINT members_email_key UNIQUE (email)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS users.members
    OWNER to postgres;
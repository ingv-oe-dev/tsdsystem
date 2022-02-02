-- Table: tsd_users.tokens

-- DROP TABLE IF EXISTS tsd_users.tokens;

CREATE TABLE IF NOT EXISTS tsd_users.tokens
(
    id SERIAL NOT NULL,
    token text,
	create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    CONSTRAINT tokens_pkey PRIMARY KEY (id)
);
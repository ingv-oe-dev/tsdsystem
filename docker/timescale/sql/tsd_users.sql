--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- Table: tsd_users.members

-- DROP TABLE IF EXISTS tsd_users.members;

CREATE TABLE IF NOT EXISTS tsd_users.members
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

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
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
)

TABLESPACE pg_default;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- Table: tsd_users.members_mapping_roles

-- DROP TABLE IF EXISTS tsd_users.members_mapping_roles;

CREATE TABLE IF NOT EXISTS tsd_users.members_mapping_roles
(
    member_id integer NOT NULL,
    role_id integer NOT NULL,
    priority integer NULL,
    create_time timestamp NULL DEFAULT timezone('utc'::text, now()),
	update_time timestamp NULL,
	remove_time timestamp NULL,
	CONSTRAINT members_mapping_roles_pkey PRIMARY KEY (member_id, role_id)
)

TABLESPACE pg_default;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- Table: tsd_users.members_permissions

-- DROP TABLE IF EXISTS tsd_users.members_permissions;

CREATE TABLE IF NOT EXISTS tsd_users.members_permissions
(
    id SERIAL NOT NULL,
    member_id integer NOT NULL,
	settings jsonb,
    active boolean,
	create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    CONSTRAINT members_permissions_pkey PRIMARY KEY (id)
)

TABLESPACE pg_default;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- Table: tsd_users.roles_permissions

-- DROP TABLE IF EXISTS tsd_users.roles_permissions;

CREATE TABLE IF NOT EXISTS tsd_users.roles_permissions
(
    id SERIAL NOT NULL,
    role_id integer NOT NULL,
	settings jsonb,
    active boolean,
	create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    CONSTRAINT roles_permissions_pkey PRIMARY KEY (id)
)

TABLESPACE pg_default;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- Table: tsd_users.tokens

-- DROP TABLE IF EXISTS tsd_users.tokens;

CREATE TABLE IF NOT EXISTS tsd_users.tokens
(
    id SERIAL NOT NULL,
    token text,
    remote_addr character varying(255) COLLATE pg_catalog."default" NOT NULL,
	create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    CONSTRAINT tokens_pkey PRIMARY KEY (id)
)

TABLESPACE pg_default;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- Table: tsd_users.temp_reset_keys

-- DROP TABLE IF EXISTS tsd_users.temp_reset_keys;

CREATE TABLE IF NOT EXISTS tsd_users.temp_reset_keys
(
    id SERIAL NOT NULL,
    email character varying(255) COLLATE pg_catalog."default" NOT NULL,
    rand_key character varying(255) COLLATE pg_catalog."default" NOT NULL,
	create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    CONSTRAINT temp_reset_keys_pkey PRIMARY KEY (id)
)

TABLESPACE pg_default;

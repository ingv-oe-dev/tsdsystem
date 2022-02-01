-- Table: tsd_users.members_permissions

-- DROP TABLE IF EXISTS tsd_users.members_permissions;

CREATE TABLE IF NOT EXISTS tsd_users.members_permissions
(
    id SERIAL NOT NULL,
    member_id integer NOT NULL,
	settings jsonb,
	create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    CONSTRAINT members_permissions_pkey PRIMARY KEY (id)
);
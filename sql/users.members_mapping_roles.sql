-- Table: tsd_users.members_mapping_roles

-- DROP TABLE IF EXISTS tsd_users.members_mapping_roles;

CREATE TABLE IF NOT EXISTS tsd_users.members_mapping_roles
(
    member_id integer NOT NULL,
    role_id integer NOT NULL,
	CONSTRAINT members_mapping_roles_pkey PRIMARY KEY (member_id, role_id)
)
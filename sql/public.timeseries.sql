-- Table: tsd_main.timeseries

-- DROP TABLE IF EXISTS tsd_main.timeseries;

CREATE TABLE IF NOT EXISTS tsd_main.timeseries
(
    id uuid NOT NULL DEFAULT uuid_generate_v4(),
    schema character varying(63) COLLATE pg_catalog."default" NOT NULL,
    name character varying(63) COLLATE pg_catalog."default" NOT NULL,
    sampling integer,
    metadata jsonb,
    last_time timestamp without time zone,
    create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer,
    CONSTRAINT timeseries_pkey PRIMARY KEY (id)
);

CREATE UNIQUE INDEX tsd_main_timeseries_lower_schema_lower_name_idx ON tsd_main.timeseries (LOWER(schema), LOWER(name))

TABLESPACE pg_default;

ALTER TABLE IF EXISTS tsd_main.timeseries
    OWNER to postgres;
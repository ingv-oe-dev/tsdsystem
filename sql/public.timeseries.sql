-- Table: public.timeseries

-- DROP TABLE IF EXISTS public.timeseries;

CREATE TABLE IF NOT EXISTS public.timeseries
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
    CONSTRAINT timeseries_pkey PRIMARY KEY (id),
    UNIQUE (schema, name)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.timeseries
    OWNER to postgres;
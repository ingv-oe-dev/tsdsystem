--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- Table: tsd_main.timeseries

-- DROP TABLE IF EXISTS tsd_main.timeseries;

CREATE TABLE IF NOT EXISTS tsd_main.timeseries
(
    -- id uuid NOT NULL DEFAULT uuid_generate_v4(),
    id uuid NOT NULL DEFAULT gen_random_uuid(),
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
    public boolean DEFAULT true,
    CONSTRAINT timeseries_pkey PRIMARY KEY (id)
);

CREATE UNIQUE INDEX tsd_main_timeseries_lower_schema_lower_name_idx ON tsd_main.timeseries (LOWER(schema), LOWER(name))

TABLESPACE pg_default;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- Table: tsd_main.timeseries_mapping_channels

-- DROP TABLE IF EXISTS tsd_main.timeseries_mapping_channels;

CREATE TABLE IF NOT EXISTS tsd_main.timeseries_mapping_channels
(
    timeseries_id uuid NOT NULL,
    channel_id integer NOT NULL,
	CONSTRAINT timeseries_mapping_channels_pkey PRIMARY KEY (timeseries_id, channel_id)
)

TABLESPACE pg_default;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- PROCEDURE: tsd_main.updateTimeseriesLastTime(character varying, character varying)

-- DROP PROCEDURE IF EXISTS tsd_main."updateTimeseriesLastTime"(character varying, character varying);

CREATE OR REPLACE PROCEDURE tsd_main."updateTimeseriesLastTime"(
	IN my_schema character varying DEFAULT NULL::character varying,
	IN my_name character varying DEFAULT NULL::character varying)
LANGUAGE 'plpgsql'
AS $BODY$
BEGIN
    EXECUTE CONCAT('UPDATE tsd_main.timeseries SET last_time = (
      SELECT LAST(time, time) 
      FROM ', my_schema, '.', my_name, 
    ') WHERE schema = ', quote_literal(my_schema), 
     ' AND name = ' , quote_literal(my_name)
    ); 
END;
$BODY$;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------

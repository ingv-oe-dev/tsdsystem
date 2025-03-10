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
    first_time timestamp without time zone,
    last_time timestamp without time zone,
    last_value jsonb,
    n_samples integer null,
    with_tz boolean DEFAULT false,
    public boolean DEFAULT true,
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
    EXECUTE CONCAT('
        WITH last_info AS (
            SELECT * FROM ', my_schema, '.', my_name, ' ORDER BY time DESC LIMIT 1
        ),
		count_info AS (
            SELECT COUNT(*) AS counter FROM ', my_schema, '.', my_name, '
        ),
		tz AS (
			SELECT with_tz FROM tsd_main.timeseries WHERE schema = ', quote_literal(my_schema), ' AND name = ' , quote_literal(my_name) , '
		)
        UPDATE tsd_main.timeseries SET
            last_time = (
                CASE
                    WHEN count_info.counter < 1 THEN NULL
                    ELSE (
                        CASE
                            WHEN tz.with_tz = TRUE THEN last_info.time at time zone ', quote_literal('utc'), '
                            ELSE last_info.time
                        END
                    )
                END
            ),
            last_value = (
                CASE
                    WHEN count_info.counter < 1 THEN NULL
                    ELSE row_to_json(last_info)
                END
            ),
			n_samples = count_info.counter
        from count_info
        LEFT JOIN tz ON true
        LEFT JOIN last_info ON true
        WHERE schema = ', quote_literal(my_schema), ' AND name = ' , quote_literal(my_name) , ';
	');
END;
$BODY$;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
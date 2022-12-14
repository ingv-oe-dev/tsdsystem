--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- Table: tsd_pnet.owners

-- DROP TABLE IF EXISTS tsd_pnet.owners;
CREATE TABLE IF NOT EXISTS tsd_pnet.owners
(
    id SERIAL NOT NULL,
    name character varying(255) COLLATE pg_catalog."default" NOT NULL,
	create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer,
    CONSTRAINT owners_pkey PRIMARY KEY (id)
)

TABLESPACE pg_default;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- Table: tsd_pnet.nets

-- DROP TABLE IF EXISTS tsd_pnet.nets;

CREATE TABLE IF NOT EXISTS tsd_pnet.nets
(
    id SERIAL NOT NULL,
    name character varying(255) COLLATE pg_catalog."default" NOT NULL,
    owner_id integer,
	create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer,
    CONSTRAINT nets_pkey PRIMARY KEY (id)
)

TABLESPACE pg_default;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- Table: tsd_pnet.sensortypes

-- DROP TABLE IF EXISTS tsd_pnet.sensortypes;

CREATE TABLE IF NOT EXISTS tsd_pnet.sensortypes
(
    id SERIAL NOT NULL,
    name character varying(255) COLLATE pg_catalog."default" NOT NULL,
	json_schema jsonb,
	create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer,
    CONSTRAINT sensortypes_pkey PRIMARY KEY (id)
)

TABLESPACE pg_default;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- Table: tsd_pnet.sites

-- DROP TABLE IF EXISTS tsd_pnet.sites;

CREATE TABLE IF NOT EXISTS tsd_pnet.sites
(
    id SERIAL NOT NULL,
    name character varying(255) COLLATE pg_catalog."default" NOT NULL,
	coords geometry,
    quote real,
	info jsonb,
	create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer,
    CONSTRAINT sites_pkey PRIMARY KEY (id)
)

TABLESPACE pg_default;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- Table: tsd_pnet.sensors

-- DROP TABLE IF EXISTS tsd_pnet.sensors;

CREATE TABLE IF NOT EXISTS tsd_pnet.sensors
(
    id SERIAL NOT NULL,
    name character varying(255) COLLATE pg_catalog."default" NOT NULL,
	coords geometry,
    quote real,
	custom_props jsonb,
	net_id integer,
    site_id integer,
    create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer,
    CONSTRAINT sensors_pkey PRIMARY KEY (id)
)

TABLESPACE pg_default;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- Table: tsd_pnet.channels

-- DROP TABLE IF EXISTS tsd_pnet.channels;

CREATE TABLE IF NOT EXISTS tsd_pnet.channels
(
    id SERIAL NOT NULL,
    name character varying(255) COLLATE pg_catalog."default" NOT NULL,
    sensor_id integer,
    sensortype_id integer,
    metadata jsonb,
    start_datetime timestamp without time zone,
    end_datetime timestamp without time zone,
    info jsonb,
	create_time timestamp without time zone DEFAULT (now() AT TIME ZONE 'utc'::text),
    update_time timestamp without time zone,
    remove_time timestamp without time zone,
    create_user integer,
    update_user integer,
    remove_user integer,
    CONSTRAINT channels_pkey PRIMARY KEY (id)
)

TABLESPACE pg_default;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- PROCEDURE: tsd_pnet.updateSensorsChannels(int4)

-- DROP PROCEDURE IF EXISTS tsd_pnet."updateSensorsChannels"(int4);

CREATE OR REPLACE PROCEDURE tsd_pnet."updateSensorsChannels"(
	IN my_sensor_id int4
)
LANGUAGE 'plpgsql'
AS $BODY$
BEGIN
    EXECUTE CONCAT('
        with
        ch_ as (
            select
                c.*
            from
                tsd_pnet.channels c
            where
                c.remove_time is null
            and c.sensor_id = ', my_sensor_id ,'
        ),
        st as (
            select
                ch_.sensortype_id
            from
                ch_
            order by ch_.end_datetime desc limit 1
        ),
        start_ as (
            select
                ch_.start_datetime
            from
                ch_
            order by ch_.start_datetime limit 1
        ),
        end_ as (
            select
                ch_.end_datetime
            from
                ch_
            order by ch_.end_datetime desc limit 1
        ),
        count_ as (
            select count(*) as n_channels 
            from ch_
        )
        UPDATE 
            tsd_pnet.sensors s
        SET 
            sensortype_id = st.sensortype_id,
            n_channels = count_.n_channels, 
            start_datetime = start_.start_datetime, 
            end_datetime = end_.end_datetime
        from st, count_, start_, end_
        where s.remove_time ISNULL AND s.id = ', my_sensor_id
    );
END;
$BODY$;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------

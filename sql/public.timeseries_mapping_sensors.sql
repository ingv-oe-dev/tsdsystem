-- Table: tsd_main.timeseries_mapping_sensors

-- DROP TABLE IF EXISTS tsd_main.timeseries_mapping_sensors;

CREATE TABLE IF NOT EXISTS tsd_main.timeseries_mapping_sensors
(
    timeseries_id uuid NOT NULL,
    sensor_id integer NOT NULL,
	CONSTRAINT timeseries_mapping_sensors_pkey PRIMARY KEY (timeseries_id, sensor_id)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS tsd_main.timeseries_mapping_sensors
    OWNER to postgres;
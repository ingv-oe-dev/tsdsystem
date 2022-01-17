-- Table: public.timeseries_mapping_channels

-- DROP TABLE IF EXISTS public.timeseries_mapping_channels;

CREATE TABLE IF NOT EXISTS public.timeseries_mapping_channels
(
    timeseries_id uuid NOT NULL,
    channel_id integer NOT NULL,
	CONSTRAINT timeseries_mapping_channels_pkey PRIMARY KEY (timeseries_id, channel_id)
)

TABLESPACE pg_default;

ALTER TABLE IF EXISTS public.timeseries_mapping_channels
    OWNER to postgres;
--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- FUNCTION: public.jsonb_recursive_merge(a jsonb, b jsonb)

-- DROP FUNCTION IF EXISTS public.jsonb_recursive_merge(a jsonb, b jsonb)

CREATE OR REPLACE FUNCTION public.jsonb_recursive_merge(a jsonb, b jsonb)
 RETURNS jsonb
 LANGUAGE sql
AS $function$ 
SELECT 
 jsonb_object_agg( 
 coalesce(ka, kb), 
 CASE 
 WHEN va isnull THEN vb 
 WHEN vb isnull THEN va 
 WHEN jsonb_typeof(va) <> 'object' OR jsonb_typeof(vb) <> 'object' THEN vb 
 ELSE jsonb_recursive_merge(va, vb) END 
 ) 
 FROM jsonb_each(A) temptable1(ka, va)
 FULL JOIN jsonb_each(B) temptable2(kb, vb) ON ka = kb 
$function$
;

--------------------------------------------------------------------------------------
--------------------------------------------------------------------------------------
-- FDSN

-- public.fdsn_station_dataset source

CREATE OR REPLACE VIEW public.fdsn_station_dataset
AS SELECT n.id AS net_id,
    n.name AS net_name,
    n.description AS net_description,
    n.additional_info AS net_additional_info,
    n.n_nodes AS totalnumberstations,
    n.starttime AS net_startdate,
    n.endtime AS net_enddate,
    s.id AS station_id,
    s.name AS station_name,
    s.coords AS station_coords,
    s.quote AS station_elevation,
    s.site_id AS station_site_id,
    s.sitename AS station_sitename,
    s.starttime AS station_startdate,
    s.endtime AS station_enddate,
    s.n_channels AS totalnumberchannels,
    c.id AS channel_id,
    c.name AS channel_name,
    c.additional_info AS channel_additional_info,
    sc.start_datetime AS channel_startdate,
    sc.end_datetime AS channel_enddate,
    sensors.id as sensor_id,
    sensors.name AS sensor_name,
    sensors.serial_number AS sensor_serial_number,
    st.name AS sensortype_name,
    st.model AS sensortype_model,
    st.response_parameters,
    d.id as digitizer_id,
    d.name AS digitizer_name,
    d.serial_number AS digitizer_serial_number,
    dt.name AS digitizertype_name,
    dt.model AS digitizertype_model,
    dt.final_sample_rate,
    dt.final_sample_rate_measure_unit,
    dt.sensitivity,
    dt.sensitivity_measure_unit,
    dt.dynamical_range,
    dt.dynamical_range_measure_unit,
    dt.additional_info->>'responseXML' AS additional_responsexml
   FROM ( SELECT n_a.id,
            n_a.name,
            n_a.description,
            n_a.additional_info,
            n_a.n_nodes,
            n_b.starttime,
            n_b.endtime
           FROM ( SELECT n_1.id,
                    n_1.name,
                    n_1.description,
                    n_1.additional_info,
                    count(s_1.id) AS n_nodes
                   FROM tsd_pnet.nets n_1
                     LEFT JOIN tsd_pnet.stations s_1 ON n_1.id = s_1.net_id AND s_1.remove_time IS NULL
                  WHERE n_1.remove_time IS NULL
                  GROUP BY n_1.id) n_a
             JOIN ( SELECT n_1.id,
                    min(c_1.start_datetime) AS starttime,
                        CASE
                            WHEN count(c_1.id) = count(c_1.end_datetime) THEN max(c_1.end_datetime)
                            ELSE NULL::timestamp without time zone
                        END AS endtime
                   FROM tsd_pnet.nets n_1
                     LEFT JOIN tsd_pnet.stations s_1 ON n_1.id = s_1.net_id AND s_1.remove_time IS NULL
                     LEFT JOIN tsd_pnet.station_configs c_1 ON s_1.id = c_1.station_id AND c_1.remove_time IS NULL
                  WHERE n_1.remove_time IS NULL
                  GROUP BY n_1.id) n_b ON n_a.id = n_b.id) n
     LEFT JOIN ( SELECT s_1.id,
            s_1.name,
            s_1.coords,
            s_1.quote,
            s_1.net_id,
            s_1.site_id,
            ss.name AS sitename,
            count(c_1.id) AS n_channels,
            min(sc_1.start_datetime) AS starttime,
                CASE
                    WHEN count(sc_1.id) = count(sc_1.end_datetime) THEN max(sc_1.end_datetime)
                    ELSE NULL::timestamp without time zone
                END AS endtime
           FROM tsd_pnet.stations s_1
             LEFT JOIN tsd_pnet.station_configs sc_1 ON sc_1.station_id = s_1.id
             LEFT JOIN tsd_pnet.channels c_1 ON sc_1.id = c_1.station_config_id AND c_1.remove_time IS NULL
             LEFT JOIN tsd_pnet.sites ss ON ss.id = s_1.site_id
          WHERE s_1.remove_time IS NULL
          GROUP BY s_1.id, ss.name) s ON s.net_id = n.id
     LEFT JOIN tsd_pnet.station_configs sc ON s.id = sc.station_id AND sc.remove_time IS NULL
     LEFT JOIN tsd_pnet.sensors sensors ON sensors.id = sc.sensor_id AND sensors.remove_time IS NULL
     LEFT JOIN tsd_pnet.sensortypes st ON st.id = sensors.sensortype_id AND st.remove_time IS NULL
     LEFT JOIN tsd_pnet.digitizers d ON d.id = sc.digitizer_id AND d.remove_time IS NULL
     LEFT JOIN tsd_pnet.digitizertypes dt ON dt.id = d.digitizertype_id AND dt.remove_time IS NULL
     LEFT JOIN tsd_pnet.channels c ON sc.id = c.station_config_id AND c.remove_time IS NULL;
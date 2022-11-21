-- public.fdsn_station source

CREATE OR REPLACE VIEW public.fdsn_station
AS SELECT n.id AS net_id,
    n.name AS net_name,
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
    c.metadata AS channel_metadata,
    c.start_datetime AS channel_startdate,
    c.end_datetime AS channel_enddate,
    c.info AS channel_additionalinfo
    FROM ( 
      SELECT n_a.*, n_b.starttime, n_b.endtime from (
        SELECT n_1.id,
            n_1.name,
            count(s_1.id) AS n_nodes
          FROM tsd_pnet.nets n_1
            LEFT JOIN tsd_pnet.sensors s_1 ON n_1.id = s_1.net_id AND s_1.remove_time IS NULL
          WHERE n_1.remove_time IS NULL
          GROUP BY n_1.id
        ) n_a
        INNER JOIN
        (
        SELECT 
          n_1.id,
          min(c_1.start_datetime) AS starttime,
            CASE WHEN count(c_1.id) = count(c_1.end_datetime) THEN max(c_1.end_datetime) ELSE NULL::timestamp without time zone END AS endtime
        FROM tsd_pnet.nets n_1
            LEFT JOIN tsd_pnet.sensors s_1 ON n_1.id = s_1.net_id AND s_1.remove_time IS NULL
            LEFT JOIN tsd_pnet.channels c_1 ON s_1.id = c_1.sensor_id AND c_1.remove_time IS NULL
        WHERE n_1.remove_time IS NULL
        GROUP BY n_1.id
        ) n_b on n_a.id = n_b.id
    ) n
     LEFT JOIN ( SELECT s_1.id,
            s_1.name,
            s_1.coords,
            s_1.quote,
            s_1.custom_props,
            s_1.net_id,
            s_1.site_id,
            s_1.create_time,
            s_1.update_time,
            s_1.remove_time,
            s_1.create_user,
            s_1.update_user,
            s_1.remove_user,
            ss.name AS sitename,
            count(c_1.id) AS n_channels,
            min(c_1.start_datetime) AS starttime,
                CASE
                    WHEN count(c_1.id) = count(c_1.end_datetime) THEN max(c_1.end_datetime)
                    ELSE NULL::timestamp without time zone
                END AS endtime
           FROM tsd_pnet.sensors s_1
             LEFT JOIN tsd_pnet.channels c_1 ON s_1.id = c_1.sensor_id AND c_1.remove_time IS NULL
             LEFT JOIN tsd_pnet.sites ss ON ss.id = s_1.site_id
          WHERE s_1.remove_time IS NULL
          GROUP BY s_1.id, ss.name) s ON s.net_id = n.id
     LEFT JOIN ( SELECT c_1.id,
            c_1.name,
            c_1.sensor_id,
            c_1.sensortype_id,
            c_1.metadata,
            c_1.start_datetime,
            c_1.end_datetime,
            c_1.info,
            c_1.create_time,
            c_1.update_time,
            c_1.remove_time,
            c_1.create_user,
            c_1.update_user,
            c_1.remove_user
           FROM tsd_pnet.channels c_1
          WHERE c_1.remove_time IS NULL) c ON c.sensor_id = s.id;
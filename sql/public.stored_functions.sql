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

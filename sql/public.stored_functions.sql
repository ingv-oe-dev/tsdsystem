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

-- Search by channel
select t.id, t.schema, t.name, 'by_channel' as search_type 
from pnet.channels c 
inner join public.timeseries_mapping_channels tmc on c.id = tmc.channel_id 
inner join public.timeseries t on tmc.timeseries_id = t.id
where c.id = 0

-- Search by sensor
select t.id, t.schema, t.name, 'by_sensor' as search_type 
from pnet.sensors s 
inner join public.timeseries_mapping_sensors tms on s.id = tms.sensor_id
inner join public.timeseries t on tms.timeseries_id = t.id
where s.id = 0

union 

select t.id, t.schema, t.name, 'by_sensor_channel' as search_type 
from pnet.sensors s 
inner join pnet.channels c on s.id = c.sensor_id 
inner join public.timeseries_mapping_channels tmc on c.id = tmc.channel_id 
inner join public.timeseries t on tmc.timeseries_id = t.id
where s.id = 0
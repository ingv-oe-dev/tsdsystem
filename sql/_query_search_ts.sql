-- Search by channel
select t.id, t.schema, t.name, 'by_channel' as search_type 
from tsd_pnet.channels c 
inner join tsd_main.timeseries_mapping_channels tmc on c.id = tmc.channel_id 
inner join tsd_main.timeseries t on tmc.timeseries_id = t.id
where c.id = 0

-- Search by sensor
select t.id, t.schema, t.name, 'by_sensor' as search_type 
from tsd_pnet.sensors s 
inner join tsd_pnet.channels c on s.id = c.sensor_id
inner join tsd_main.timeseries_mapping_channels tmc on c.id = tmc.channel_id 
inner join tsd_main.timeseries t on tmc.timeseries_id = t.id
where s.id = 0

-- mapping dependencies from timeseries to nets
select
	tmc.timeseries_id, tmc.channel_id, c.sensor_id, s.net_id 
from
	tsd_main.timeseries t
left join tsd_main.timeseries_mapping_channels tmc on
	t.id = tmc.timeseries_id
left join tsd_pnet.channels c on
	tmc.channel_id  = c.id
left join tsd_pnet.sensors s on
	c.sensor_id = s.id
left join tsd_pnet.nets n on
	s.net_id = n.id
where t.id ='fb965447-8294-4a8a-b312-839ada78e7c1'"
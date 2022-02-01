<?php

$dsn = "pgsql:host=localhost;dbname=tsdsystem;";
$myConnection = new PDO($dsn, "postgres", "development");

// query per controllo settaggio dei permessi per rete e/o sensori e/o canali e/o serie temporali
/*
$sqlQuery = "select
	tmc.*, c.sensor_id, s.net_id 
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
where t.id ='fb965447-8294-4a8a-b312-839ada78e7c1'";
*/

// query per controllo settaggio dei permessi solo per rete e/o serie temporali
$sqlQuery = "select
	tmc.timeseries_id, s.net_id 
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
where t.id ='fb965447-8294-4a8a-b312-839ada78e7c1'";

$sqlResult = $myConnection->query($sqlQuery);
$array_one = $sqlResult->fetchAll(PDO::FETCH_ASSOC);
$array_two = array();
foreach ($array_one as $key => $item) {
    foreach ($item as $subkey => $subitem) {
        if (isset($subitem)) {
            $array_two[$subkey][$key] = $subitem;
        }
    }
}
foreach ($array_two as $key => $item) {
    $array_two[$key] = array_unique($array_two[$key]);
}
var_export($array_two);

/*
ESEMPI DI CONTROLLI (A DUE PASSI**):

** Il passo [1] può essere evitato se, al momento del rilascio del token, inserisco il json che rappresenta il permesso dentro il payload del token.

Il lancio della query sopra (dentro il punto [2]) NON può essere evitato ad ogni richiesta.
-> Per velocizzare le operazioni di lettura di queste info, una soluzione potrebbe essere la costituzione di una materialized_view (che è a tutti gli effetti una tabella) con la query sopra, la quale viene aggiornata ad ogni operazione di modifica delle tabelle coinvolte: timeseries; timeseries_mapping_channels; channels; sensors; nets.

*** SCRITTURA valori serie temporale con id = X:
    -- SQL (PostGreSQL) -> jsonb_path_query(<permissions_table_name>.<jsonb_field_name>, '$.resources.timeseries.edit')

    [1] token -> user_id -> role_id -> leggo i permessi assegnati a questo ruolo
            user_id -> leggo se esistono permessi assegnati a questo utente (in caso sovrapponi ai permessi assegnati al ruolo [^]) 
                [^] è possibile usare la funzione PHP array_merge_recursive() opp. la funzione POSTGRESQL jsonb_recursive_merge();

            i permessi sono del tipo:
            "edit": {
                "enabled": true,
                "ip": [],
                "permissions": {
                    "net_id": null,
                    "sensor_id": null,
                    "channel_id": null,
                    "id" : null
                }
            }

    [2] Controllo quindi se:
        - edit->enabled == true
        - se il mio ip è nella lista edit->ip oppure se ip è vuoto (tutti consentiti)
        - se non esiste il campo edit->permissions vuol dire che è consentito qualsiasi scrittura, altrimenti vai nello specifico edit->permissions:
            - se la serie temporale con id = X è nella lista edit->permissions->id 
            - se no lancia la query sopra per avere l'$array_two e controlla se:
                (- il channel_id della serie temporale con id = X è nella lista edit->permissions->channel_id)*opzionale, se si sceglie di usare la query commentata
                (- il sensor_id della serie temporale con id = X è nella lista edit->permissions->sensor_id)*opzionale, se si sceglie di usare la query commentata
                - il net_id della serie temporale con id = X è nella lista edit->permissions->net_id

*** LETTURA valori serie temporale con id = X:
    -- SQL (PostGreSQL) -> jsonb_path_query(<permissions_table_name>.<jsonb_field_name>, '$.resources.timeseries.read')

    [1] token -> user_id -> role_id -> leggo i permessi assegnati a questo ruolo
            user_id -> leggo se esistono permessi assegnati a questo utente (in caso sovrapponi ai permessi assegnati al ruolo [^]) 
                [^] è possibile usare la funzione PHP array_merge_recursive() opp. la funzione POSTGRESQL jsonb_recursive_merge();

            i permessi sono del tipo:
            "read": {
				"enabled": true,
				"ip": [],
				"permissions": {
					"all": {
						"start_period": null,
						"end_period": null,
						"number_of_days": 1,
						"last_days": true
					},
					"net_id": {
						"<net_id_1>": {
							"start_period": null,
							"end_period": null,
							"number_of_days": 1,
							"last_days": true
						},
						"<net_id_2>": {},
						"<net_id_n>": {}
					},
					"sensor_id": {
						"<sensor_id_1>": {
							"start_period": null,
							"end_period": null,
							"number_of_days": 1,
							"last_days": true
						},
						"<sensor_id_2>": {},
						"<sensor_id_n>": {}
					},
					"channel_id": {
						"<channel_id_1>": {
							"start_period": null,
							"end_period": null,
							"number_of_days": 1,
							"last_days": true
						},
						"<channel_id_2>": {},
						"<channel_id_n>": {}
					},
					"id": {
						"<timeseries_id_1>": {
							"start_period": null,
							"end_period": null,
							"number_of_days": 1,
							"last_days": true
						},
						"<timeseries_id_2>": {},
						"<timeseries_id_n>": {}
					}
				}
			}


    [2] Controllo quindi se:
        - read->enabled == true
        - se il mio ip è nella lista read->ip oppure se ip è vuoto (tutti consentiti)
        - se non esiste il campo read->permissions vuol dire che è consentito qualsiasi tipo di richiesta di dati, altrimenti vai nello specifico read->permissions:
            - se la serie temporale con id = X è nella lista read->permissions->id, allora controlla per mezzo della configurazione read->permissions->id->X
            - se no lancia la query sopra per avere l'$array_two e controlla se:
                (- il channel_id della serie temporale con id = X è nella lista read->permissions->channel_id->channel_id_of_X)*opzionale, se si sceglie di usare la query commentata
                (- il sensor_id della serie temporale con id = X è nella lista read->permissions->sensor_id->sensor_id_of_X)*opzionale, se si sceglie di usare la query commentata
                - il net_id della serie temporale con id = X è nella lista read->permissions->net_id->net_id_ofX
            - altrimenti controlla per mezzo della configurazione read->permissions->all

*/
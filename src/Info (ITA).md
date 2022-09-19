# Come utilizzare il web service del TSDSystem (*test per amministratori*)

## Operazioni iniziali
Unica variabile d'ambiente da settare **necessariamente**  è `SERVER_KEY`.  
Il resto si può provare ad ignorare; in tal caso testare se tutto funziona ugualmente. Sicuramente non verranno inviate email all'utente che registrerete.  
> Ricordo a tal proposito che nell'ultimo commit ho previsto l'inserimento di una variabile d'ambiente `REG_PATTERN`, utilizzata dallo script che gestisce la registrazione dell'utente, come pattern per l'email accettate dal sistema.  
> Quindi, dato che non la valorizzerete, il sistema accetterà qualsiasi nome vogliate utilizzare al momento (*finora era stato utilizzato un pattern che accettasse solo indirizzi email INGV*)

## Registrazione utente
Vi sarà possibile registrare l'utente direttamente tramite richiesta HTTP, senza passare dall'interfaccia web, chiamando il seguente URL:

http://localhost/tsdws/login/registration.php  

### Esempio di chiamata HTTP (POST): 
```
POST /tsdws/login/registration.php HTTP/1.1  
Host: localhost  
Content-Type: application/x-www-form-urlencoded  
Content-Length: 65  
email=carmelocassisi%40gmail.com&password=mypassword&send_mail=false
```
### Risposta con successo:
```
{"status":true,"message":"Success: You have been registered!","salt":"099e4023569ab4675d1e6cf37b31d78c7ad61ce93e9ffecfac13d7377edf4396bb7ff04ff3bdcea38367745f51e7c9d5da1363a52855d779d44c62bc8556703a"}
```
Oltre al messaggio di conferma ritornata dal POST, potrete controllare l'avvenuta registrazione dalla tabella del database:

`tsdsystem > tsd_users > members`


> Per fare in modo che l'utente venga **effettivamente abilitato** ad eseguire richieste al web service,valorizzare nel record di pertinenza dell'utente, il campo `confirmed` (simulando la conferma da parte dell'amministratore quando riceve l'email). Per fare ciò potrete tranquillamente copiare il valore che trovate in `registered`.

## Autorizzare l'utente a manipolare la risorsa Timeseries
Per fare in modo che l'utente possa registrare una nuova serie temporale, inserire valori e leggerne il contenuto, è necessario configurare i relativi permessi. La strada più veloce per fare ciò è inserire un nuovo record nella tabella:  

`tsdsystem > tsd_users > member_permissions`

dove, al campo `member_id` si inserirà il relativo `id` del record dell'utente in `tsdsystem > tsd_users > members`, e assegnando al campo `settings` il seguente JSON:
```
{
    "resources": {
        "timeseries": {
            "edit": {
                "ip": [], 
                "enabled": true
            }, 
            "read": {
                "ip": [], 
                "enabled": true
            }
        }
    }
}
``` 

## I token JWT (JSON Web Tokens)
Qualora le richieste al web service vengano fatte dall'interfaccia web predisposta, le autorizzazioni e i permessi assegnati agli utenti vengono riconosciuti tramite le informazioni contenute in sessione (valorizzata in fase di login nell'interfaccia).
Qualsiasi altra richiesta che non provenga dall'interfaccia web, necessita di un **token** di autorizzazione.

Per ottenere un token, il web service REST predispone il seguente endpoint:

http://localhost/tsdws/token

### Esempio di chiamata HTTP (POST):
> 
```
POST /tsdws/token HTTP/1.1
Host: localhost
Content-Type: application/x-www-form-urlencoded
Content-Length: 61
email=carmelocassisi%40gmail.com&password=mypassword
```
### Risposta con successo:
```
{"error":null,"statusCode":201,"token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VySWQiOjI0LCJuYmYiOjE2NTcxOTM0MDAsImV4cCI6MTY1OTc4NTQwMH0.yZP7bsgLYgn_4Ys4fQiuvL2Y5mPF4PpyP3YQ1CGAF74"}
```
L'esempio mostrato ritornerà un token in cui vengono inserite (nel payload), l'`id` dell'utente ed eventualmente tutti i permessi assegnati all'utente, o per lo specifico *scope*.
Nel caso in cui, ad esempio, si voglia ottenere un token specifico per leggere serie temporali, allora è possibile specificare lo *scope* `timeseries-read` o `timeseries-edit` per uno specifico per la scrittura. **Esempio**:

```
POST /tsdws/token HTTP/1.1
Host: localhost
Content-Type: application/x-www-form-urlencoded
Content-Length: 61
email=carmelocassisi%40gmail.com&password=mypassword&scope=timeseries-edit
```
Una volta ottenuto il token JWT, è possibile passarne il contenuto nell'header delle successive richieste HTTP, nel campo `Authorization`.

## Registrare una nuova Timeseries
La registrazione di una nuova serie temporale può essere effettuata tramite l'endpoint:

http://localhost/tsdws/timeseries

passando un JSON come da esempio mostrato sotto.

### Esempio di chiamata HTTP (POST):
```
POST /tsdws/timeseries HTTP/1.1
Host: localhost
Authorization: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VySWQiOjI0LCJuYmYiOjE2NTcxOTM0MDAsImV4cCI6MTY1OTc4NTQwMH0.yZP7bsgLYgn_4Ys4fQiuvL2Y5mPF4PpyP3YQ1CGAF74
Content-Type: application/json
Content-Length: 496
{
    "schema": "oedatarep",
    "name": "weather_metrics",
    "sampling": 600,
    "columns": [
        {
            "name":"temp_c",
            "type":"double precision"
        },
        {
            "name":"pressure_hpa",
            "type":"double precision"
        },
        {
            "name":"humidity_percent",
            "type":"double precision"
        },
        {
            "name":"wind_speed_ms",
            "type":"double precision"
        }
    ]
}
```
L'esempio mostrato indica che la serie con nome "*weather_metrics*" deve essere salvata in uno schema specifico "*oedatarep*" (se non esiste lo crea), ed ha un campionamento a 600 secondi (vedi campo `sampling` - *serve al database TimescaleDB per capire che tipologia di split utilizzare per le tabelle che ne conterranno i valori*).  

Il campo `columns` elenca le metriche relative alla serie temporale (es. per serie temporale multiparametriche). E' un array di coppie (`name`, `type`), dove per `name` va bene qualsiasi stringa (di lunghezza < 255 caratteri) e per `type` (*string*) viene indicato qualsiasi formato numerico accettato da PostGreSQL (es. *smallint* | *integer* | *double precision*). **Nel caso più comune è un array di lunghezza = 1** (serie univariata).
> **Non è necessario indicare la colonna relativa al tempo** (creata di default con nome `time`). 

Nel caso in cui non viene specificato il campo `columns`, il web service utilizzerà un valore default, del tipo (serie univariata):
```
"columns": [
    {
        "name":"value",
        "type":"double precision"
    }
]
```
### Risposta con successo
La risposta con successo alla suddetta richiesta sarà un JSON come il seguente:
```
{
    "params": {
        "schema": "oedatarep",
        "name": "weather_metrics",
        "sampling": 600,
        "columns": [
            {
                "name": "temp_c",
                "type": "double precision"
            },
            {
                "name": "pressure_hpa",
                "type": "double precision"
            },
            {
                "name": "humidity_percent",
                "type": "double precision"
            },
            {
                "name": "wind_speed_ms",
                "type": "double precision"
            }
        ],
        "metadata": {
            "columns": [
                {
                    "name": "temp_c",
                    "type": "double precision"
                },
                {
                    "name": "pressure_hpa",
                    "type": "double precision"
                },
                {
                    "name": "humidity_percent",
                    "type": "double precision"
                },
                {
                    "name": "wind_speed_ms",
                    "type": "double precision"
                }
            ]
        }
    },
    "data": {
        "status": true,
        "rows": 1,
        "id": "985dc570-7298-4ae6-b4f9-1546d51b879a",
        "mapping_result": {
            "status": true
        }
    },
    "error": null,
    "statusCode": 201
}
```
L'informazione principale risiede nel campo `data`, che indica l'`id` della nuova serie temporale registrata.

### Lista delle Timeseries registrate
Lo stesso endpoint:

http://localhost/tsdws/timeseries

può essere utilizzato per fare una richiesta HTTP di tipo GET per ottenere l'elenco di tutte le serie temporali registrate.
```
GET /github/tsdsystem/src/tsdws/timeseries/ HTTP/1.1
Host: localhost
Authorization: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VySWQiOjE2LCJuYmYiOjE2NTcxOTM2NDUsImV4cCI6MTY1OTc4NTY0NX0.dwqyDfFLH7MDnXi2bOJWuGsYf8kNcwEcIg2IUBecOyU
Content-Type: application/json
```

## Inserire i valori su una Timeseries registrata
La registrazione di una nuova serie temporale può essere effettuata tramite l'endpoint:

http://localhost/tsdws/timeseries/values

passando un JSON come da esempio mostrato sotto.

### Esempio di chiamata HTTP (POST):
```
POST /tsdws/timeseries/values HTTP/1.1
Host: localhost
Authorization: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VySWQiOjE2LCJuYmYiOjE2NTcxOTM2NDUsImV4cCI6MTY1OTc4NTY0NX0.dwqyDfFLH7MDnXi2bOJWuGsYf8kNcwEcIg2IUBecOyU
Content-Type: application/json
Cookie: PHPSESSID=cdqjpd1o6vijtbb6qmb1mk5a9i
Content-Length: 286

{
    "id": "985dc570-7298-4ae6-b4f9-1546d51b879a",
    "insert": "ignore",
    "columns": ["time","temp_c","pressure_hpa"],
    "data": [
        ["2022-01-04 15:26:00",100,70],
        ["2022-01-04 15:27:00",10,55],
        ["2022-01-04 15:28:00",null,155]
    ]
}
```
### Risposta con successo:
```
{
    "params": null,
    "data": {
        "status": true,
        "rows": 3,
        "updatedTimeseriesTable": true
    },
    "error": null,
    "statusCode": 201
}
```
L'informazione principale risiede nel campo `data`, che indica il numero di record correttamente inseriti (campo `rows`).

## Leggere i valori di una Timeseries registrata
Per leggere i valori inseriti si utilizza sempre lo stesso endpoint:

http://localhost/tsdws/timeseries/values

ma la richiesta HTTP è di tipo **GET**.  
Unico parametro richiesto nella querystring è `request`, una stringa JSON che incorpora tutti i parametri che caratterizzano la richiesta della serie temporale.

### Esempio di chiamata HTTP (GET):
```
GET /tsdws/timeseries/values?request={"id":"985dc570-7298-4ae6-b4f9-1546d51b879a","starttime":"2022-01-04%2014:00:00","endtime":"2022-01-04%2016:00:00","columns":["temp_c","pressure_hpa"],"time_bucket":"1%20hour","aggregate":"AVG"} HTTP/1.1
Host: localhost
Authorization: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VySWQiOjE2LCJuYmYiOjE2NTcxOTM2NDUsImV4cCI6MTY1OTc4NTY0NX0.dwqyDfFLH7MDnXi2bOJWuGsYf8kNcwEcIg2IUBecOyU
```
> - Unico campo che è necessario specificare dentro `request` è `id`.  
> - Se `time_bucket` e `aggregate` non vengono specificate, il web server ritornerà tutti i record con `time` compreso tra `starttime` e `endtime`, senza applicare nessuna funzione di aggregazione (o sottocampionamento).  
> - Se neanche `starttime` e `endtime` vengono indicati, il periodo di default selezionato saranno le ultime 24 ore.  
> - Se non viene specificato l'array `columns`, il web service ritornerà i valori di tutte le metriche (colonne).
> 
### Risposta con successo:
```
{
    "params": {
        "id": "985dc570-7298-4ae6-b4f9-1546d51b879a",
        "starttime": "2022-01-04 14:00:00",
        "endtime": "2022-01-04 16:00:00",
        "columns": [
            {
                "name": "temp_c"
            },
            {
                "name": "pressure_hpa"
            }
        ],
        "time_bucket": "1 hour",
        "aggregate": "AVG",
        "transpose": false
    },
    "data": {
        "timestamp": [
            "2022-01-04 14:00:00",
            "2022-01-04 15:00:00",
            "2022-01-04 16:00:00"
        ],
        "temp_c": [
            null,
            55,
            null
        ],
        "pressure_hpa": [
            null,
            93.33333333333333,
            null
        ]
    },
    "error": null,
    "statusCode": 200
}
```
Anche in questo caso, l'informazione principale risiede nel campo `data`, composto da un array per ogni colonna della serie. La colonna relativa al tempo è indicata con la chiave `timestamp`, mentre le altre colonne (dei valori) portano il nome assegnato in fase di registrazione.  
**Nota**: il nome di default per le serie univariate è `value`, quindi nel caso più comune il campo `data` sarà nel seguente formato:
```
"data": {
    {
        "timestamp": [],
        "value": []
    }
}
```
Il campo `params` riassume il tipo di richiesta nel formato JSON. Da notare che dentro questo campo vengono specificati parametri che non necessariamente vengono esplicitati dall'utente, ma prendono un valore di default, come `transpose` (di default `false`). In particolare, riguardo a quest'ultimo parametro, se viene valorizzato a `true`, la risposta alla richiesta sarà come la seguente, ovvero con il campo `data` "trasposto":
```
{
    "params": {
        "id": "b0c77d19-5b6e-4162-9aa0-1073c48b9de0",
        "starttime": "2022-01-04 14:00:00",
        "endtime": "2022-01-04 16:00:00",
        "columns": [
            {
                "name": "temp_c"
            },
            {
                "name": "pressure_hpa"
            }
        ],
        "transpose": true
    },
    "data": [
        {
            "timestamp": "2022-01-04 15:26:00",
            "temp_c": 100,
            "pressure_hpa": 70
        },
        {
            "timestamp": "2022-01-04 15:27:00",
            "temp_c": 10,
            "pressure_hpa": 55
        },
        {
            "timestamp": "2022-01-04 15:28:00",
            "temp_c": null,
            "pressure_hpa": 155
        }
    ],
    "error": null,
    "statusCode": 200
}
```
 
const stationMapComponentDefinition = {
    template: `<div></div>`,
    props: [
        "mapSelector",
        "markers",
        "selectedMarker"
    ],
    data() {
        return {
            zoom_bounds: null,
            circle_marker_radius: 8, // raggio in px del marker circolare
            mymap: null,
            markerArray: []
        }
    },
    mounted() {
        this.initialize();
    },
    computed: {

    },
    methods: {
        initialize() {
            // instanzio la mappa per il div con id='mymap'
            this.mymap = L.map(this.$el, {
                zoomControl: false // aggiungo il zoomControl successivamente (personalizzato)
            }).setView(L.latLng(37.5, 14.5), 8);

            // instanzio il layer di base da openstreetmap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(this.mymap);

            // aggiungo il zoomControl personalizzato
            new L.Control.Zoom({ position: 'bottomleft' }).addTo(this.mymap);
        },
        // funzione per il plotting dei markers
        plotOnMap(data) {

            let self = this;

            for (var i = 0; i < data.length; i++) {
                // assegno alla variabile item l'ennesimo record in data
                var item = data[i];

                try {
                    // crea il marker circolare passando lat e lon tramite la funzione di riproiezione L.latLng
                    let marker = L.circleMarker(L.latLng(item.coords.coordinates[1], item.coords.coordinates[0]), {
                            radius: this.circle_marker_radius,
                            color: "#000",
                            weight: 1,
                            opacity: 1,
                            fillColor: "#999", // prendo il colore dal valore ritornato dal db
                            fillOpacity: 1,
                            customProp: item, // aggiungi le info dell'item come proprietÃ  custom ('customProp' Ã¨ una key scelta arbitrariamente)
                        }) // le prossime tre funzioni in basso sono concatenate

                    // aggiungo il marker nell'array dei marker
                    if (this.zoom_bounds && this.zoom_bounds != '') {
                        try {
                            let bounds = zoom_bounds.split(',');
                            if (item.coords.coordinates[1] <= bounds[1] && item.coords.coordinates[1] >= bounds[0] && item.coords.coordinates[0] <= bounds[3] && item.coords.coordinates[0] >= bounds[2]) {
                                this.markerArray.push(marker);
                            }
                        } catch (e) {
                            this.markerArray.push(marker);
                        }
                    } else {
                        this.markerArray.push(marker);
                    }

                    marker.addTo(this.mymap) // aggiungi marker alla mappa
                        .bindPopup(this.makePopup(item)) // aggiungi popup al marker (testo per il popup ritornato dalla funzione makePopup(item))
                        .on('click', function() {
                            let selected = this.options.customProp.id;
                            self.$emit('clickedMarker', selected);
                        }); // per test => per vedere nella console, sul click del marker, i valori custom assegnati  

                    // crea la label per il marker sfruttando L.marker e L.divIcon (il testo della label Ã¨ nell'attributo 'html' di divIcon)
                    L.marker(L.latLng(item.coords.coordinates[1], item.coords.coordinates[0]), {
                        icon: L.divIcon({
                            html: item.name,
                            iconAnchor: [this.circle_marker_radius * 2, -this.circle_marker_radius],
                            className: 'circle-marker-label', // con la classe definisco anche lo stile del testo (style css della classe 'circle-marker-label' definito nella pagina html)
                        })
                    }).addTo(this.mymap);
                } catch (e) {
                    // do nothing
                }
            }

            // fit la mappa nel bound che rinchiude tutti i marker
            try {
                var group = new L.featureGroup(this.markerArray);
                this.mymap.fitBounds(group.getBounds());
            } catch (e) {}

        },
        // funzione di creazione del testo html da inserire nel popup, tramite le info dell'item
        makePopup(item) {
            var html = "<h6>" + item.name + "</h6>";
            html += "<div><b>Coordinates</b> (WGS84):</div>" +
                "<div><i>Lat:</i> " + item.coords.coordinates[1] + "&deg; N</div>" +
                "<div><i>Lon:</i> " + item.coords.coordinates[0] + "&deg; E</div>" +
                "<div><i>Quote:</i> " + item.quote + " m</div>";
            return html;
        }
    },
    watch: {

    }
};
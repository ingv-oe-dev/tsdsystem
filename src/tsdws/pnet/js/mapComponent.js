const mapComponentDefinition = {
    template: `<div></div>`,
    data() {
        return {
            initial_view: {
                zoom: 8,
                lat: 37.5,
                lon: 14.5
            },
            circle_marker_radius: 8, // raggio in px del marker circolare
            map: null,
            layerSwitch: null,
            overlayMaps: {}
        }
    },
    mounted() {
        this.initialize();
    },
    computed: {

    },
    methods: {
        initialize() {
            // instanzio la mappa
            this.map = L.map(this.$el, {
                zoomControl: false, // aggiungo il zoomControl successivamente (personalizzato),
                zoomAnimation: false
            }).setView(L.latLng(this.initial_view.lat, this.initial_view.lon), this.initial_view.zoom);

            // instanzio il layer di base da openstreetmap
            let osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(this.map);

            // definisco i gruppi di layer
            let baseMaps = {
                "OpenStreetMap": osm
            };

            // aggiungo il layer switch
            this.layerSwitch = new L.control.layers(baseMaps, null, { collapsed: true }).addTo(this.map);

            // aggiungo il zoomControl personalizzato
            new L.Control.Zoom({ position: 'topright' }).addTo(this.map);

            // aggiungo la scale bar
            new L.control.scale({position: 'bottomright'}).addTo(this.map);
        },
        // funzione per il plotting dei nodi
        plotStationsOnMap(data, options) {
            //console.log(data, options);
            var self = this;
            this.resetLayers(options);

            for (var i = 0; i < data.length; i++) {
                // assegno alla variabile item l'ennesimo record in data
                var item = data[i];

                try {
                    // crea il marker circolare passando lat e lon tramite la funzione di riproiezione L.latLng
                    let marker = L.circleMarker(L.latLng(item.coords.coordinates[1], item.coords.coordinates[0]), {
                            radius: this.circle_marker_radius,
                            color: "#000",
                            weight: 1,
                            opacity: (item.old_station ? 0.5 : 1),
                            fillColor: colors.nets[item.net_id ? item.net_id % paletteLength : 0].backgroundColor, // prendo il colore dal valore ritornato dal db
                            fillOpacity: (item.old_station ? 0.5 : 1),
                            customProp: item, // aggiungi le info dell'item come proprietÃ  custom ('customProp' Ã¨ una key scelta arbitrariamente)
                            className: (item.old_station ? 'old_station-marker' : '')
                        }) // le prossime tre funzioni in basso sono concatenate

                    marker.bindPopup(this.makePopup(item), {closeOnClick: false, autoClose: false}) // aggiungi popup al marker (testo per il popup ritornato dalla funzione makePopup(item))
                        .on('click', function() {
                            let selected = this.options.customProp;
                            selected.marker_type = 'station';
                            self.$emit('clicked-marker', selected);
                        }); // per test => per vedere nella console, sul click del marker, i valori custom assegnati  

                    // aggiungo il marker nel layer corrente
                    this.overlayMaps[options.group_id].addLayer(marker);

                    // crea la label per il marker sfruttando L.marker e L.divIcon (il testo della label Ã¨ nell'attributo 'html' di divIcon)
                    let label = L.marker(L.latLng(item.coords.coordinates[1], item.coords.coordinates[0]), {
                        icon: L.divIcon({
                            html: item.name,
                            iconAnchor: [this.circle_marker_radius * 2, -this.circle_marker_radius],
                            className: 'circle-marker-label' + (item.old_station ? ' old_station-marker' : ''), // con la classe definisco anche lo stile del testo (style css della classe 'circle-marker-label' definito nella pagina html)
                            customProp: {
                                "id": "label_station_" + item.id
                            }
                        })
                    })

                    // aggiungo la label nel layer corrente
                    this.overlayMaps[options.group_id].addLayer(label);

                } catch (e) {
                    // do nothing
                }
            }

            try {
                // aggiungo il layer corrente dei marker alla mappa
                this.overlayMaps[options.group_id].addTo(this.map);
                this.fitBounds();
            } catch (e) {
                console.log(e);
            }

        },
        // funzione per il plotting dei siti
        plotSites(data, options) {
            //console.log(data, options);
            var self = this;

            // reset layer
            if (options.group_id && this.overlayMaps[options.group_id]) {
                if (!options.append) {
                    this.overlayMaps[options.group_id].clearLayers();
                }
            } else {
                this.overlayMaps[options.group_id] = new L.FeatureGroup();
                // aggiorno anche il layer switch control
                this.layerSwitch.addOverlay(this.overlayMaps[options.group_id], "<img src='img/flag.png' style='height:1em'></span> " + options.group_id);
            }

            for (var i = 0; i < data.length; i++) {
                // assegno alla variabile item l'ennesimo record in data
                var item = data[i];

                try {
                    if (item.coords.type == "Polygon") {
                        let latlng = [];
                        for (var i = 0; i < item.coords.coordinates[0].length - 1; i++) {
                            c = item.coords.coordinates[0][i];
                            latlng.push([c[1], c[0]]);
                        }
                        console.log(latlng);
                        let polygon = L.polygon(latlng);
                        this.overlayMaps[options.group_id].addLayer(polygon);
                    }

                    // crea il marker circolare passando lat e lon tramite la funzione di riproiezione L.latLng
                    let marker = L.marker(L.latLng(item.centroid.coordinates[1], item.centroid.coordinates[0]), {
                        icon: L.icon({
                            iconUrl: 'img/flag.png',
                            iconSize: [32, 32],
                            additionalInfo: item
                        })
                    });

                    marker.bindPopup(this.makePopupSite(item), {closeOnClick: false, autoClose: false}) // aggiungi popup al marker (testo per il popup ritornato dalla funzione makePopup(item)) 
                        .on('click', function() {
                            let selected = this.options.icon.options.additionalInfo;
                            selected.marker_type = 'site';
                            self.$emit('clicked-marker', selected);
                            selected_marker = this;
                            // list stations belonging to the selected site
                            $.ajax({
                                url: options.baseURLws + "stations",
                                data: { "site_id": selected.id },
                                beforeSend: function(jqXHR, settings) {
                                    jqXHR = Object.assign(jqXHR, settings, { "messageText": "Loading stations for site (id=" + item.id + ")" });
                                    $("#site"+selected.id+"_marker_popup_station_list").html("Loading stations...");
                                },
                                success: function(response, textStatus, jqXHR) {
                                    selected_marker.setPopupContent(self.updatePopupSite(response.data, selected.id));
                                    let n = Object.assign(jqXHR, { "messageType": "info" });
                                    self.$emit('loading-from-map', n);
                                },
                                error: function(jqXHR) {
                                    let n = Object.assign(jqXHR, { "messageType": "danger" });
                                    self.$emit('loading-from-map', n);
                                }
                            });
                        }); // per test => per vedere nella console, sul click del marker, i valori custom assegnati  

                    // aggiungo il marker nel layer corrente
                    this.overlayMaps[options.group_id].addLayer(marker);

                    // crea la label per il marker sfruttando L.marker e L.divIcon (il testo della label Ã¨ nell'attributo 'html' di divIcon)
                    let label = L.marker(L.latLng(item.centroid.coordinates[1], item.centroid.coordinates[0]), {
                        icon: L.divIcon({
                            html: item.name,
                            iconAnchor: [this.circle_marker_radius * 2, -this.circle_marker_radius],
                            className: 'circle-marker-label', // con la classe definisco anche lo stile del testo (style css della classe 'circle-marker-label' definito nella pagina html)
                            additionalInfo: {
                                "id": "label_site_" + item.id
                            }
                        })
                    })

                    // aggiungo la label nel layer corrente
                    this.overlayMaps[options.group_id].addLayer(label);

                } catch (e) {
                    // do nothing
                }
            }

            try {
                // aggiungo il layer corrente dei marker alla mappa
                this.overlayMaps[options.group_id].addTo(this.map);
                // nascondo il layer se utilizzo l'opzione 'show' = false
                if (!options.show) this.map.removeLayer(this.overlayMaps[options.group_id]);
                //this.fitBounds();
            } catch (e) {
                console.log(e);
            }

        },
        resetLayers(options) {
            if (options.group_id && this.overlayMaps[options.group_id]) {
                if (!options.append) {
                    this.overlayMaps[options.group_id].clearLayers();
                }
            } else {
                this.overlayMaps[options.group_id] = new L.FeatureGroup();
                // aggiorno anche il layer switch control
                this.layerSwitch.addOverlay(this.overlayMaps[options.group_id], "<span class='dot' style='background-color:" + colors.nets[options.net_id % paletteLength].backgroundColor + "'></span> " + options.group_id);
            }
        },
        // funzione di creazione del testo html da inserire nel popup, tramite le info dell'item
        makePopup(item) {
            var html = "<h6>STATION: <b>" + item.name + "</b> ("+item.net_name+")</h6>";
            html += "<div><b>Coordinates</b> (WGS84):</div>" +
                "<div><i>Lat:</i> " + item.coords.coordinates[1] + "&deg; N</div>" +
                "<div><i>Lon:</i> " + item.coords.coordinates[0] + "&deg; E</div>" +
                "<div><i>Quote:</i> " + item.quote + " m</div>";
            return html;
        },
        makePopupSite(item) {
            var html = "<h6>SITE: <b>" + item.name + "</b></h6>";
            html += "<div><b>Coordinates (of centroid for polygons)</b> (WGS84):</div>" +
                "<div><i>Lat:</i> " + item.centroid.coordinates[1] + "&deg; N</div>" +
                "<div><i>Lon:</i> " + item.centroid.coordinates[0] + "&deg; E</div>" +
                "<div><i>Quote:</i> " + item.quote + " m</div><br>";
            html += "<div><b>GeoJSON</b> (WGS84 coordinates):</div>" +
                "<div>" + JSON.stringify(item.coords) + "</div>";
            html += "<br/><div id='site"+item.id+"_marker_popup_station_list'></div>";
            return html;
        },
        updatePopupSite(data, site_id) {
            var self = this;
            var html = "<b>Stations in site:</b><ul>";
            $.each(data, function(index, item) {
                html += "<li><b>"+item.name+"</b> ("+item.net_name+") <span id='site"+site_id+"_marker_popup_station"+item.id+"' style='cursor:pointer'>[<u>filter in station list</u>]</span></li>";
            });
            html += "</ul>";
            $("#site"+site_id+"_marker_popup_station_list").html(html);
            $.each(data, function(index, item) {
                $("span#site"+site_id+"_marker_popup_station"+item.id).on("click", function() {
                    self.$emit('clicked-marker', {marker_type: "station", name: item.name, net_id: item.net_id});
                });
            });
        },
        openPopupById(id) {
            let layer_name_list = Object.keys(this.overlayMaps);
            for (let i = 0; i < layer_name_list.length; i++) {
                let markers = this.overlayMaps[layer_name_list[i]].getLayers();
                for (let j = 0; j < markers.length; j++) {
                    let marker = markers[j];
                    //console.log(marker);
                    if (marker.options && marker.options.customProp && marker.options.customProp.id === id) {
                        marker.openPopup();
                        return marker;
                    }
                }
            }
        },
        openSitePopupById(id) {
            let markers = this.overlayMaps['sites'].getLayers();
            for (let i = 0; i < markers.length; i++) {
                let marker = markers[i];
                if (marker.options && marker.options.icon && marker.options.icon.options && marker.options.icon.options.additionalInfo && marker.options.icon.options.additionalInfo.id === id) {
                    marker.openPopup();
                    return marker;
                }
            }
        },
        zoomToMarkerById(marker, zoomLevel) {
            this.map.setView(marker.getLatLng(), zoomLevel);
        },
        fitBounds() {
            let bounds = new L.latLngBounds();
            let layer_name_list = Object.keys(this.overlayMaps);
            for (let i = 0; i < layer_name_list.length; i++) {
                bounds.extend(this.overlayMaps[layer_name_list[i]].getBounds());
            }
            try {
                // fit la mappa nel bound che rinchiude tutti i marker
                this.map.fitBounds(bounds);
            } catch (e) {
                //console.log(e);
            }
        },
        removeMarkerById(id) {
            let layer_name_list = Object.keys(this.overlayMaps);
            for (var i = 0; i < layer_name_list.length; i++) {
                let markers = this.overlayMaps[layer_name_list[i]];
                for (var j = 0; j < markers.length; j++) {
                    let marker = markers[j];
                    console.log(marker);
                    if (marker.options && marker.options.customProp && (marker.options.customProp.id === id || marker.options.customProp.id === "label_station_" + id)) {
                        markers.removeLayer(marker);
                    }
                }
            }
        }
    },
    watch: {

    }
};
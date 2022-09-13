const {
    createApp
} = Vue

var app = {
    mounted() {
        this.notifications = this.$refs.notifications.list;
        this.$nextTick(() => {
            window.addEventListener('resize', this.onResize);
        });
        this.navBtnClick();
        this.initLoad();
    },
    beforeDestroy() {
        window.removeEventListener('resize', this.onResize);
    },
    data() {
        return {
            isActive: {
                "L": true, // Left side panel
                "R": false, // Right side panel
                "N": false // Notifications panel
            },
            baseURLws: "http://localhost/github/tsdsystem/src/tsdws/",
            windowWidth: window.innerWidth,
            resetID: 0,
            nets: [],
            sensortypes: [],
            sites: [],
            sensors: [],
            sensorsList: [],
            channelsList: {},
            timeseriesList: {},
            defaultOption: {
                nets: "--- Select ---",
                sensortypes: "--- Select ---",
                sites: "--- Select ---"
            },
            filters: {
                name: "",
                net_id: 0,
                sensortype_id: 0,
                site_id: 0
            },
            checkedAllSensors: false,
            sorting: 'byNetAndName',
            loadingMessages: {
                "sensors": "No items"
            },
            notifications: []
        }
    },
    methods: {
        onResize() {
            this.windowWidth = window.innerWidth
        },
        navBtnClick(a) {
            // exclude others div on small windows
            if (this.windowWidth < 769) {
                for (var key in this.isActive) {
                    if (key != a) {
                        this.isActive[key] = false;
                    }
                }
            }
            // toggle button isActive
            this.isActive[a] = !this.isActive[a];
        },
        initLoad() {
            //console.log("init load");
            this.fetchNets();
            this.fetchSensortypes();
            this.fetchSites();
            this.fetchSensors();
            //this.$forceUpdate();
        },
        fetchNets() {
            this.defaultOption.nets = "Loading...";
            this.nets = [];

            let self = this;
            $.ajax({
                url: self.baseURLws + "nets",
                success: function(response) {
                    self.nets = response.data;
                    self.defaultOption.nets = "--- Select ---";
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    self.defaultOption.nets = errorThrown + " - Loading failed";
                }
            });
        },
        fetchSensortypes() {
            this.defaultOption.sensortypes = "Loading...";
            this.sensortypes = [];

            let self = this;
            $.ajax({
                url: self.baseURLws + "sensortypes",
                success: function(response) {
                    self.sensortypes = response.data;
                    self.defaultOption.sensortypes = "--- Select ---";
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    self.defaultOption.sensortypes = errorThrown + " - Loading failed";
                }
            });
        },
        fetchSites() {
            this.defaultOption.sites = "Loading...";
            this.sites = [];

            let self = this;
            $.ajax({
                url: self.baseURLws + "sites",
                success: function(response) {
                    self.sites = response.data;
                    self.defaultOption.sites = "--- Select ---";
                    self.$refs.leafmap.plotSites(self.sites, { "group_id": "sites", "append": false });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    self.defaultOption.sites = errorThrown + " - Loading failed";
                }
            });
        },
        fetchSensors(parameters = {
            "deep": true
        }) {
            this.sensors = [];

            let self = this;
            $.ajax({
                url: self.baseURLws + "sensors",
                data: parameters,
                success: function(response) {
                    //console.log(response);
                    self.sensors = response.data;
                    self.applyFilters();
                    self.handlePlot();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    self.loadingMessages.sensors = errorThrown + " - Loading failed";
                }
            });
        },
        removeSensor(id) {
            let self = this;
            if (confirm("Are you sure to remove this node?")) {
                $.ajax({
                    url: self.baseURLws + "sensors?id=" + id,
                    type: "DELETE",
                    success: function(response) {
                        console.log(response);
                        self.fetchSensors();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert(errorThrown);
                    }
                });
            }
        },
        fetchChannels(parameters) {
            let self = this;
            $.ajax({
                url: self.baseURLws + "channels",
                data: parameters,
                beforeSend: function(jqXHR, settings) {
                    jqXHR = Object.assign(jqXHR, settings);
                },
                success: function(response) {
                    self.channelsList[parameters.sensor_id] = response.data;
                    for (let i = 0; i < response.data.length; i++) {
                        self.fetchTimeseries({ "channel_id": response.data[i].id, "hidden": true });
                    }
                    //console.log(self.channelsList);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    self.channelsList[parameters.sensor_id] = [];
                    self.$refs.notifications.notify(jqXHR, 'danger');
                }
            });
        },
        removeChannel(id, sensor_id) {
            let self = this;
            if (confirm("Are you sure to remove this channel?")) {
                $.ajax({
                    url: self.baseURLws + "channels?id=" + id,
                    type: "DELETE",
                    success: function(response) {
                        console.log(response);
                        self.fetchChannels({ sensor_id: sensor_id });
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert(errorThrown);
                    }
                });
            }
        },
        fetchTimeseries(parameters) {
            let self = this;
            $.ajax({
                url: self.baseURLws + "timeseries",
                data: parameters,
                success: function(response) {
                    self.timeseriesList[parameters.channel_id] = response.data;
                    self.timeseriesList[parameters.channel_id].hidden = parameters.hidden;
                    console.log(self.timeseriesList);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    self.timeseriesList[parameters.sensor_id] = [];
                }
            });
        },
        onMapMarkerClick(el) {
            console.log(el);
            switch (el.marker_type) {
                case 'node':
                    this.filters.name = el.name;
                    break;
                case 'site':
                    //this.filters.site_id = el.id;
                    break;
                default:
                    break;
            }

        },
        applyFilters(filters = {}) {
            //console.log(filters);

            // Sort the array based on 1) net_id and 2) name [and make a deep copy!]
            let items = this.sensors.sort(this.sorting == 'byName' ? this.sortByName : this.sortByNetAndName);
            //console.log(items);
            // apply filters
            this.sensorsList = items.filter(function(item) {
                let check_condition = true;
                if (filters.name && filters.name != "") check_condition = check_condition && item.name.toLowerCase().includes(filters.name.toLowerCase());
                if (filters.net_id) check_condition = check_condition && item.net_id == filters.net_id;
                if (filters.sensortype_id) check_condition = check_condition && item.sensortype_id == filters.sensortype_id;
                if (filters.site_id) check_condition = check_condition && item.site_id == filters.site_id;
                //console.log(filters, item, check_condition);
                return check_condition;
            });
        },
        sortByName(a, b) {
            if (a.name <= b.name) return -1;
            else return 1;
        },
        sortByNetAndName(a, b) {
            if (a.net_id < b.net_id) return -1;
            if (a.net_id == b.net_id) {
                if (a.name <= b.name) return -1;
                else return 1;
            }
            return 1;
        },
        handlePlot() {
            if (this.sensorsList.length === 0) return;

            let no_net_name = "No net";
            // force (un)plotting of "no net" nodes
            this.$refs.leafmap.plotNodesOnMap([], { "group_id": no_net_name, "net_id": null, "append": false })

            let init_net_id = -1;
            let init_net_name = '';
            let toPlot = [];
            let element = null;
            for (let i = 0; i < this.sensorsList.length; i++) {
                //console.log(i);
                element = this.sensorsList[i];
                // essendo ordinati sempre per net_id posso permettermi di passare in rassegna l'array e richiamare la funzione di plot non appena net_id cambia
                if (init_net_id != -1 && element.net_id != init_net_id) {
                    //console.log(init_net_id, element.net_id, init_net_name);
                    this.$refs.leafmap.plotNodesOnMap(toPlot, { "group_id": init_net_name, "net_id": init_net_id, "append": false });
                    toPlot = [];
                }
                toPlot.push(element);
                init_net_id = element.net_id;
                init_net_name = element.net_name ? element.net_name : no_net_name;
            }
            this.$refs.leafmap.plotNodesOnMap(toPlot, { "group_id": init_net_name, "net_id": init_net_id, "append": false })
        },
        getNetColor(net_id) {
            if (net_id) return net_colors[net_id];
            return net_colors[this.resetID];
        },
        getSensortypeColor(sensortype_id) {
            if (sensortype_id) return sensortype_colors[sensortype_id];
            return sensortype_colors[this.resetID];
        },
        getAbbreviation(str) {
            try {
                return str.substr(0, 1);
            } catch (e) {
                return '/';
            }
        },
        listElementAction(sensor_id) {
            this.toggleChannelList(sensor_id);
            this.selectMapMarker(sensor_id);
        },
        toggleChannelList(sensor_id) {
            //console.log(sensor_id);
            if (this.channelsList[sensor_id]) {
                this.channelsList[sensor_id].hidden = !this.channelsList[sensor_id].hidden;
            } else {
                this.fetchChannels({ "sensor_id": sensor_id });
            }
        },
        toggleTimeseriesList(channel_id) {
            //console.log(sensor_id);
            if (this.timeseriesList[channel_id]) {
                this.timeseriesList[channel_id].hidden = !this.timeseriesList[channel_id].hidden;
            } else {
                this.fetchTimeseries({ "channel_id": channel_id });
            }
        },
        selectMapMarker(sensor_id) {
            //console.log(sensor_id);
            let marker = this.$refs.leafmap.openPopupById(sensor_id);
            //console.log(marker);
            this.$refs.leafmap.zoomToMarkerById(marker, zoomLevel = 15);
        },
        selectSiteOnMap(site_id) {
            //console.log(sensor_id);
            let marker = this.$refs.leafmap.openSitePopupById(site_id);
            //console.log(marker);
            this.$refs.leafmap.zoomToMarkerById(marker, zoomLevel = 15);
        },
        openEdit(resource_type, id, additionalInfo = {}) {
            switch (resource_type) {
                case 'net':
                    link = this.baseURLws + "form/edit/nets.php";
                    if (id) link += "?id=" + id;
                    this.loadFrame({
                        src: link,
                        resource_type: resource_type,
                        id: id
                    });
                    break;
                case 'sensortype':
                    link = this.baseURLws + "form/edit/sensortypes.php";
                    if (id) link += "?id=" + id;
                    this.loadFrame({
                        src: link,
                        resource_type: resource_type,
                        id: id
                    });
                    break;
                case 'site':
                    link = this.baseURLws + "form/edit/sites.php";
                    if (id) link += "?id=" + id;
                    this.loadFrame({
                        src: link,
                        resource_type: resource_type,
                        id: id
                    });
                    break;
                case 'sensor':
                    link = this.baseURLws + "form/edit/sensors.php";
                    if (id) link += "?id=" + id;
                    this.loadFrame({
                        src: link,
                        resource_type: resource_type,
                        id: id
                    });
                    break;
                case 'channel':
                    link = this.baseURLws + "form/edit/channels.php";
                    if (id) {
                        link += "?id=" + id;
                    } else {
                        link += "?dummy=1";
                    };
                    if (additionalInfo.sensor_id !== undefined) link += "&sensor_id=" + additionalInfo.sensor_id;

                    this.loadFrame({
                        src: link,
                        resource_type: resource_type,
                        id: id,
                        sensor_id: additionalInfo.sensor_id ? additionalInfo.sensor_id : null
                    });
                    break;
                case 'timeseries':
                    link = this.baseURLws + "form/edit/timeseries.php";
                    if (id) {
                        link += "?id=" + id;
                    } else {
                        link += "?dummy=1";
                    };
                    if (additionalInfo.channel_id !== undefined) link += "&channel_id=" + additionalInfo.channel_id;
                    this.loadFrame({
                        src: link,
                        resource_type: resource_type,
                        id: id ? id : null,
                        channel_id: additionalInfo.channel_id ? additionalInfo.channel_id : null,
                        sensor_id: additionalInfo.sensor_id ? additionalInfo.sensor_id : null
                    });
                    break;
                default:
                    break;
            }
            // open editor on rightside
            if (!this.isActive["R"]) this.navBtnClick("R");
        },
        // handling events captured from frame containing resource edit form
        loadFrame(opt) {
            var self = this;
            window.document.addEventListener('toParentEvent', function(e) {
                switch (opt.resource_type) {
                    case 'net':
                        self.fetchNets();
                        break;
                    case 'sensortype':
                        self.fetchSensortypes();
                        break;
                    case 'site':
                        self.fetchSites();
                        break;
                    case 'sensor':
                        self.fetchSensors();
                        self.$refs.notifications.notify(e.detail, 'danger');
                        break;
                    case 'channel':
                        self.fetchChannels({ sensor_id: opt.sensor_id });
                        break;
                    case 'timeseries':
                        //self.fetchChannels({ sensor_id: opt.sensor_id });
                        self.fetchTimeseries({ channel_id: opt.channel_id });
                        break;
                    default:
                        break;
                }

            }, false);
            let iframe = document.querySelector('#sideR iframe');
            iframe.setAttribute("src", opt.src);
        }
    },
    watch: {
        isActive: {
            handler(val) {
                // show div based on key
                for (var key in val) {
                    val[key] ? $("#side" + key).show() : $("#side" + key).hide();
                }
            },
            deep: true
        },
        sensors: {
            handler(a, b) {
                //console.log(a, b);
            },
            deep: true
        },
        filters: {
            handler(val, old_val) {
                this.applyFilters(val);
                if (val.site_id != 0) {
                    this.selectSiteOnMap(val.site_id);
                }
            },
            deep: true
        },
        sensorsList: {
            handler(a, b) {
                //console.log(a, b);
            },
            deep: true
        },
        windowWidth() {
            if (this.isSmallWidth) {
                // check if more than one nav buttons are active
                let active = 0;
                let firstActive = null;
                for (var key in this.isActive) {
                    if (this.isActive[key]) {
                        active++;
                        if (active == 1) firstActive = key;
                    };
                }
                if (active > 1) {
                    // off all buttons except firstActive
                    for (var key in this.isActive) {
                        this.isActive[key] = false;
                    }
                    this.isActive[firstActive] = true;
                }
            }
        },
        checkedAllSensors(a, b) {
            this.sensorsList.forEach(element => {
                element.checked = a;
            });
        },
        sorting(val) {
            this.applyFilters();
        },
        notifications(val) {
            console.log(val);
        }
    },
    computed: {
        isSmallWidth() {
            return this.windowWidth < 769;
        },
        notificationSize() {
            let counter = 0;
            for (var i = 0; i < Object.keys(this.notifications).length; i++) {
                if (!this.notifications[Object.keys(this.notifications)[i]].messageRead) {
                    counter++;
                }
            }
            return counter;
        }
    }
};

$.ajax({
    "url": "leftside.html",
    "success": function(data) {
        $("#sideL").html(data);
        $.ajax({
            "url": "rightside.html",
            "success": function(data) {
                $("#sideR").html(data);
                createApp(app)
                    .component("leafmap", mapComponentDefinition)
                    .component("notifications", notificationListComponentDefinition)
                    .mount("#app");
            }
        })
    }
});
const {
    createApp
} = Vue

var app = {
    mounted() {
        this.notifications = this.$refs.notifications.list;
        this.lastNotify = this.$refs.notifications.defaultNotify;
        this.initToast();
        this.$nextTick(() => {
            window.addEventListener('resize', this.onResize);
        });
        this.navBtnClick();
        this.initLoad();
        this.handleCustomEvents();
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
            baseURLws: "/github/tsdsystem/src/tsdws/",
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
            //checkedAllSensors: false,
            sorting: 'byNetAndName',
            loadingMessages: {
                "sensors": "No items"
            },
            notifications: {},
            notifyAllMessages: false,
            lastNotify: {},
            showSettings: true,
            toast: [],
            toastDelay: 2000
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
        openSettings(show = true) {
            if (!this.isActive["R"]) this.navBtnClick("R");
            this.showSettings = show;
        },
        initLoad() {
            //console.log("init load");
            this.fetchNets();
            this.fetchSensortypes();
            this.fetchSites();
            this.fetchSensors();
            //this.$forceUpdate();
        },
        initToast() {
            var el = $(".toast")[0];
            this.toast = new bootstrap.Toast(el);
        },
        handleCustomEvents() {
            var self = this;
            window.document.addEventListener('netEdit', function(e) {
                //console.log(e);
                self.fetchNets();
                self.$refs.notifications.notify(e.detail);
            }, false);
            window.document.addEventListener('sensortypeEdit', function(e) {
                //console.log(e);
                self.fetchSensortypes();
                self.$refs.notifications.notify(e.detail);
            }, false);
            window.document.addEventListener('siteEdit', function(e) {
                //console.log(e);
                self.fetchSites();
                self.$refs.notifications.notify(e.detail);
            }, false);
            window.document.addEventListener('sensorEdit', function(e) {
                //console.log(e);
                self.fetchSensors();
                self.$refs.notifications.notify(e.detail);
            }, false);
            window.document.addEventListener('channelEdit', function(e) {
                //console.log(e);
                self.fetchChannels({ sensor_id: e.detail.sensor_id });
                self.$refs.notifications.notify(e.detail);
            }, false);
            window.document.addEventListener('timeseriesEdit', function(e) {
                //console.log(e);
                //self.fetchChannels({ sensor_id: e.detail.sensor_id });
                self.fetchTimeseries({ channel_id: e.detail.channel_id });
                self.$refs.notifications.notify(e.detail);
            })
        },
        fetchNets() {
            this.defaultOption.nets = "Loading...";
            this.nets = [];

            let self = this;
            $.ajax({
                url: self.baseURLws + "nets",
                data: {
                    sort_by: "name"
                },
                beforeSend: function(jqXHR, settings) {
                    jqXHR = Object.assign(jqXHR, settings, { "messageText": "Loading nets" });
                },
                success: function(response, textStatus, jqXHR) {
                    self.nets = response.data;
                    self.defaultOption.nets = "--- Select ---";
                    let n = Object.assign(jqXHR, { "messageType": "info" });
                    self.$refs.notifications.notify(n);
                },
                error: function(jqXHR) {
                    self.defaultOption.nets = "Loading failed";
                    let n = Object.assign(jqXHR, { "messageType": "danger" });
                    self.$refs.notifications.notify(n);
                }
            });
        },
        fetchSensortypes() {
            this.defaultOption.sensortypes = "Loading...";
            this.sensortypes = [];

            let self = this;
            $.ajax({
                url: self.baseURLws + "sensortypes",
                data: {
                    sort_by: "name"
                },
                beforeSend: function(jqXHR, settings) {
                    jqXHR = Object.assign(jqXHR, settings, { "messageText": "Loading sensortypes" });
                },
                success: function(response, textStatus, jqXHR) {
                    self.sensortypes = response.data;
                    self.defaultOption.sensortypes = "--- Select ---";
                    let n = Object.assign(jqXHR, { "messageType": "info" });
                    self.$refs.notifications.notify(n);
                },
                error: function(jqXHR) {
                    self.defaultOption.sensortypes = "Loading failed";
                    let n = Object.assign(jqXHR, { "messageType": "danger" });
                    self.$refs.notifications.notify(n);
                }
            });
        },
        fetchSites() {
            this.defaultOption.sites = "Loading...";
            this.sites = [];

            let self = this;
            $.ajax({
                url: self.baseURLws + "sites",
                data: {
                    sort_by: "name"
                },
                beforeSend: function(jqXHR, settings) {
                    jqXHR = Object.assign(jqXHR, settings, { "messageText": "Loading sites" });
                },
                success: function(response, textStatus, jqXHR) {
                    self.sites = response.data;
                    self.defaultOption.sites = "--- Select ---";
                    self.$refs.leafmap.plotSites(self.sites, { "group_id": "sites", "append": false });
                    let n = Object.assign(jqXHR, { "messageType": "info" });
                    self.$refs.notifications.notify(n);
                },
                error: function(jqXHR) {
                    self.defaultOption.sites = "Loading failed";
                    let n = Object.assign(jqXHR, { "messageType": "danger" });
                    self.$refs.notifications.notify(n);
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
                data: Object.assign(parameters, { "sort_by": "name" }),
                beforeSend: function(jqXHR, settings) {
                    jqXHR = Object.assign(jqXHR, settings, { "messageText": "Loading sensors" });
                },
                success: function(response, textStatus, jqXHR) {
                    //console.log(response);
                    self.sensors = response.data;
                    self.applyFilters();
                    self.handlePlot();
                    let n = Object.assign(jqXHR, { "messageType": "info" });
                    self.$refs.notifications.notify(n);
                },
                error: function(jqXHR) {
                    self.loadingMessages.sensors = "Loading failed";
                    let n = Object.assign(jqXHR, { "messageType": "danger" });
                    self.$refs.notifications.notify(n);
                }
            });
        },
        removeSensor(id) {
            let self = this;
            if (confirm("Are you sure to remove this node?")) {
                $.ajax({
                    url: self.baseURLws + "sensors?id=" + id,
                    type: "DELETE",
                    beforeSend: function(jqXHR, settings) {
                        jqXHR = Object.assign(jqXHR, settings, { "messageText": "Remove sensor [id=" + id + "]" });
                    },
                    success: function(response, textStatus, jqXHR) {
                        console.log(response);
                        self.fetchSensors();
                        let n = Object.assign(jqXHR, { "messageType": "success" });
                        self.$refs.notifications.notify(n);
                    },
                    error: function(jqXHR) {
                        let n = Object.assign(jqXHR, { "messageType": "danger" });
                        self.$refs.notifications.notify(n);
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
                    jqXHR = Object.assign(jqXHR, settings, { "messageText": "Loading channels" });
                },
                success: function(response, textStatus, jqXHR) {
                    self.channelsList[parameters.sensor_id] = response.data;
                    for (let i = 0; i < response.data.length; i++) {
                        self.fetchTimeseries({ "channel_id": response.data[i].id, "hidden": true });
                    }
                    let n = Object.assign(jqXHR, { "messageType": "info" });
                    self.$refs.notifications.notify(n);
                },
                error: function(jqXHR) {
                    self.channelsList[parameters.sensor_id] = [];
                    let n = Object.assign(jqXHR, { "messageType": "danger" });
                    self.$refs.notifications.notify(n);
                }
            });
        },
        removeChannel(id, sensor_id) {
            let self = this;
            if (confirm("Are you sure to remove this channel?")) {
                $.ajax({
                    url: self.baseURLws + "channels?id=" + id,
                    type: "DELETE",
                    beforeSend: function(jqXHR, settings) {
                        jqXHR = Object.assign(jqXHR, settings, { "messageText": "Remove channel [id=" + id + "]" });
                    },
                    success: function(response, textStatus, jqXHR) {
                        console.log(response);
                        self.fetchChannels({ sensor_id: sensor_id });
                        let n = Object.assign(jqXHR, { "messageType": "success" });
                        self.$refs.notifications.notify(n);
                    },
                    error: function(jqXHR) {
                        let n = Object.assign(jqXHR, { "messageType": "danger" });
                        self.$refs.notifications.notify(n);
                    }
                });
            }
        },
        fetchTimeseries(parameters) {
            let self = this;
            $.ajax({
                url: self.baseURLws + "timeseries",
                data: Object.assign(parameters, { "listCol": true, "sort_by": "name" }),
                beforeSend: function(jqXHR, settings) {
                    jqXHR = Object.assign(jqXHR, settings, { "messageText": "Loading timeseries" });
                },
                success: function(response, textStatus, jqXHR) {
                    self.timeseriesList[parameters.channel_id] = response.data;
                    self.timeseriesList[parameters.channel_id].hidden = parameters.hidden;
                    let n = Object.assign(jqXHR, { "messageType": "info" });
                    self.$refs.notifications.notify(n);
                },
                error: function(jqXHR) {
                    self.timeseriesList[parameters.sensor_id] = [];
                    let n = Object.assign(jqXHR, { "messageType": "danger" });
                    self.$refs.notifications.notify(n);
                }
            });
        },
        onMapMarkerClick(el) {
            //console.log(el);
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

            $("#sideR #editing").html("<iframe style='width:100%; height:calc(100vh - 54px); border-bottom: 1px solid gray;'></iframe>");
            let iframe = document.querySelector('#sideR #editing iframe');
            let link = this.baseURLws;

            switch (resource_type) {

                // net
                case 'net':
                    link += "form/edit/nets.php";
                    if (id) link += "?id=" + id;
                    iframe.setAttribute("src", link);
                    break;

                    // sensortype
                case 'sensortype':
                    link += "form/edit/sensortypes.php";
                    if (id) link += "?id=" + id;
                    iframe.setAttribute("src", link);
                    break;

                    // site
                case 'site':
                    link += "form/edit/sites.php";
                    if (id) link += "?id=" + id;
                    iframe.setAttribute("src", link);
                    break;

                    // sensor
                case 'sensor':
                    link += "form/edit/sensors.php";
                    if (id) link += "?id=" + id;
                    iframe.setAttribute("src", link);
                    break;

                    // channel
                case 'channel':
                    link += "form/edit/channels.php";
                    if (id) {
                        link += "?id=" + id;
                    } else {
                        link += "?dummy=1";
                    };
                    if (additionalInfo.sensor_id !== undefined) link += "&sensor_id=" + additionalInfo.sensor_id;
                    iframe.setAttribute("src", link);
                    break;

                    // timeseries
                case 'timeseries':
                    link += "form/edit/timeseries.php";
                    if (id) {
                        link += "?id=" + id;
                    } else {
                        link += "?dummy=1";
                    };
                    if (additionalInfo.channel_id !== undefined) link += "&channel_id=" + additionalInfo.channel_id;
                    if (additionalInfo.sensor_id !== undefined) link += "&sensor_id=" + additionalInfo.sensor_id;
                    iframe.setAttribute("src", link);
                    break;

                    // default
                default:
                    break;
            }
            // open editor on rightside
            this.openSettings(false);
        },
        openTSViewer(timeseries_id, columns = null) {

            // generate a request_id for a request
            function uuidv4() {
                return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, c =>
                    (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
                );
            }

            let params = {
                "request_id": uuidv4(),
                "title": "",
                "id": timeseries_id,
                "columns": columns
            };

            if (columns) {
                /*
                let self = this;
                $.ajax({
                    url: self.baseURLws + "timeseries/values",
                    data: {
                        "request": JSON.stringify(params)
                    },
                    beforeSend: function(jqXHR, settings) {
                        jqXHR = Object.assign(jqXHR, settings, { "messageText": "Loading timeseries values" });
                    },
                    success: function(response, textStatus, jqXHR) {
                        //console.log(response);
                        let n = Object.assign(jqXHR, { "messageType": "info" });
                        self.$refs.notifications.notify(n);
                    },
                    error: function(jqXHR) {
                        let n = Object.assign(jqXHR, { "messageType": "danger" });
                        self.$refs.notifications.notify(n);
                    }
                });
                */
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = "../form/plot-response/";
                form.target = '_blank';
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'requests';
                input.value = JSON.stringify([params]);
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
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
        /*
        checkedAllSensors(a, b) {
            this.sensorsList.forEach(element => {
                element.checked = a;
            });
        },*/
        sorting(val) {
            this.applyFilters();
        },
        notifications: {
            handler(val) {
                if (Object.keys(val).length > 0) {
                    let item = val[Object.keys(val)[Object.keys(val).length - 1]];
                    //console.log(item);
                    if (item.id !== this.lastNotify.id && (this.notifyAllMessages || (item.messageType == 'danger' || item.messageType == 'warning'))) {
                        this.lastNotify = Object.assign({}, item);
                    }
                }
            },
            deep: true
        },
        notifyAllMessages(val) {
            //console.log(val);
        },
        lastNotify: {
            handler(val) {
                if (val.id != null && !val.messageRead) {
                    this.toast.show();
                }
            },
            deep: true
        }
    },
    computed: {
        isSmallWidth() {
            return this.windowWidth < 769;
        },
        notificationSize() {
            let counter = 0;
            for (n in this.notifications) {
                this.notifications[n].messageRead ? null : counter++;
            }
            return counter - this.alertSize;
        },
        alertSize() {
            let counter = 0;
            for (n in this.notifications) {
                !this.notifications[n].messageRead && (this.notifications[n].messageType == 'danger' || this.notifications[n].messageType == 'warning') ? counter++ : null;
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
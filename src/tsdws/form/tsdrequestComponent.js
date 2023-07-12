const tsdformRequestComponentDefinition = {
    props: ["request"],
    data() {
        return {
            searchbyname: {
                disabled: true,
                options: [
                    { text: 'ON', value: false },
                    { text: 'OFF', value: true },
                ],
                typingTimer: null,
                doneTypingInterval: 500
            },
            resetID: 0,
            nets: [],
            stations: [],
            station_configs: [],
            channels: [],
            timeseries: [],
            timeseries_columns: [],
            selectedTimeseriesColumns: [],
            filterTimeseriesName: '',
            filtered_timeseries: [],
            selectedFilteredTimeseries: 0,
            previous_selected: {
                net_id: 0,
                station_id: 0,
                station_config_id: 0,
                channel_id: 0,
                timeseries_id: 0
            },
            selected: {
                net_id: 0,
                station_id: 0,
                station_config_id: 0,
                channel_id: 0,
                timeseries_id: 0
            },
            firstMapping: {
                net_id: 0,
                station_id: 0,
                station_config_id: 0,
                channel_id: 0,
                timeseries_id: 0
            },
            defaultOption: {
                nets: "--- Select net ---",
                stations: "--- Select station ---",
                station_configs: "--- Select configuration ---",
                channels: "--- Select channel ---",
                timeseries: "--- Select timeseries ---",
                filtered_timeseries: "--- Filtered timeseries ---"
            },
            aggregation: {
                disabled: false,
                options: [
                    { text: 'ON', value: false },
                    { text: 'OFF', value: true },
                ],
                functions: ["AVG", "MEDIAN", "COUNT", "MAX", "MIN", "SUM"],
                selectedFunction: null,
                timeTypes: ["second", "minute", "hour", "day", "hour", "year"],
                selectedTimeType: null,
                selectedTimeTypeCount: null
            },
            period: {
                firstAvailable: null,
                lastAvailable: null,
                start: {
                    Date: null,
                    DateSelected: null,
                    isValidDate: false,
                    Time: null,
                    isValidTime: false
                },
                end: {
                    Date: null,
                    DateSelected: null,
                    isValidDate: false,
                    Time: null,
                    isValidTime: false
                }
            },
            initialRequest: Object.assign({}, this.request),
            showStationMap: true
        }
    },
    mounted() {
        this.initForm();
        this.initialFetch();
        this.showStationMap = false;
    },
    computed: {
        searchbyname_active() {
            return !this.searchbyname.disabled;
        },
        alertMinTimeWindow() {
            return this.aggregation.selectedTimeTypeCount < 60 && this.aggregation.selectedTimeType == "second";
        }
    },
    methods: {
        initialFetch() {
            this.fetchNets();
        },
        initForm() {
            this.aggregation.selectedFunction = "AVG";
            this.aggregation.selectedTimeType = "minute";
            this.aggregation.selectedTimeTypeCount = 5;
            if (this.initialRequest.starttime) {
                this.period.start.Date = this.initialRequest.starttime.substring(0, 10);
                this.period.start.Time = this.initialRequest.starttime.substring(11, 19)
            }
            if (this.initialRequest.endtime) {
                this.period.end.Date = this.initialRequest.endtime.substring(0, 10);
                this.period.end.Time = this.initialRequest.endtime.substring(11, 19);
            }
        },
        onContextStartPeriod(ctx) {
            // The following will be an empty string until a valid date is entered
            this.period.start.DateSelected = ctx.selectedYMD
        },
        onContextEndPeriod(ctx) {
            // The following will be an empty string until a valid date is entered
            this.period.end.DateSelected = ctx.selectedYMD
        },
        validateHhMm(value) {
            return /^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$/.test(value);
        },
        fetchNets() {
            this.defaultOption.nets = "Loading...";
            this.nets = [];

            let self = this;
            axios
                .get("../nets")
                .then(response => {
                    self.nets = response.data.data;
                    self.defaultOption.nets = "--- Select net ---";
                    try {
                        if (self.searchbyname_active) {
                            self.selected.net_id = (self.firstMapping.net_id && self.firstMapping.net_id !== null) ? self.firstMapping.net_id : self.resetID;
                        } else {
                            self.selected.net_id = self.nets[0].id;
                        }
                    } catch (e) {
                        self.selected.net_id = self.resetID;
                    }
                })
                .catch(function(error) {
                    self.defaultOption.nets = "Loading failed";
                });
        },
        fetchStations() {
            this.defaultOption.stations = "Loading...";
            this.stations = [];

            let self = this;
            axios
                .get("../stations/?net_id=" + self.selected.net_id)
                .then(response => {
                    self.stations = response.data.data;
                    self.defaultOption.stations = "--- Select station ---";
                    try {
                        if (self.searchbyname_active) {
                            self.selected.station_id = (self.firstMapping.station_id && self.firstMapping.station_id !== null) ? self.firstMapping.station_id : self.resetID;
                        } else {
                            self.selected.station_id = self.stations[0].id;
                        }
                    } catch (e) {
                        self.selected.station_id = self.resetID;
                    }
                    self.$refs.stationMap.plotOnMap(self.stations, self.selected.station_id);
                })
                .catch(function() {
                    self.defaultOption.stations = "Loading failed";
                });
        },
        fetchStationConfigs() {
            this.defaultOption.station_configs = "Loading...";
            this.station_configs = [];

            let self = this;
            axios
                .get("../stations/configs/?station_id=" + self.selected.station_id + "&sort_by=start_datetime.desc")
                .then(response => {
                    self.station_configs = response.data.data;
                    self.defaultOption.station_configs = "--- Select configuration ---";
                    try {
                        if (self.searchbyname_active) {
                            self.selected.station_config_id = (self.firstMapping.station_config_id && self.firstMapping.station_config_id !== null) ? self.firstMapping.station_config_id : self.resetID;
                        } else {
                            self.selected.station_config_id = self.station_configs[0].id;
                        }
                    } catch (e) {
                        self.selected.station_config_id = self.resetID;
                    }
                })
                .catch(function() {
                    self.defaultOption.station_configs = "Loading failed";
                });
        },
        fetchChannels() {
            this.defaultOption.channels = "Loading...";
            this.channels = [];

            let self = this;
            axios
                .get("../channels/?station_config_id=" + self.selected.station_config_id)
                .then(response => {
                    self.channels = response.data.data;
                    self.defaultOption.channels = "--- Select channel ---";
                    try {
                        if (self.searchbyname_active) {
                            self.selected.channel_id = (self.firstMapping.channel_id && self.firstMapping.channel_id !== null) ? self.firstMapping.channel_id : self.resetID;
                        } else {
                            self.selected.channel_id = self.channels[0].id;
                        }
                    } catch (e) {
                        self.selected.channel_id = self.resetID;
                    }
                })
                .catch(function() {
                    self.defaultOption.channels = "Loading failed";
                });
        },
        fetchTimeseries() {
            this.defaultOption.timeseries = "Loading...";
            this.timeseries = [];
            let url = "../timeseries/?channel_id=" + this.selected.channel_id;

            let self = this;
            axios
                .get(url)
                .then(response => {
                    if (response.data.data.length > 0) {
                        self.timeseries = response.data.data;
                    } else if (self.searchbyname_active) {
                        self.timeseries = JSON.parse(JSON.stringify(self.filtered_timeseries));
                    }
                    try {
                        if (self.searchbyname_active) {
                            self.selected.timeseries_id = (self.firstMapping.timeseries_id && self.firstMapping.timeseries_id !== null) ? self.firstMapping.timeseries_id : self.resetID;
                        } else {
                            self.selected.timeseries_id = self.timeseries[0].id;
                        }
                    } catch (e) {
                        self.selected.timeseries_id = self.resetID;
                    }
                    self.defaultOption.timeseries = "--- Select timeseries ---";
                })
                .catch(function() {
                    self.defaultOption.timeseries = "Loading failed";
                });
        },
        fetchFilteredTimeseries(name) {
            this.defaultOption.filtered_timeseries = "Searching...";
            let self = this;

            clearTimeout(this.searchbyname.typingTimer);

            this.searchbyname.typingTimer = setTimeout(function() {
                this.filtered_timeseries = [];
                let url = "../timeseries/?name=" + name;

                axios
                    .get(url)
                    .then(response => {

                        try {
                            self.filtered_timeseries = response.data.data;
                            self.selectedFilteredTimeseries = self.resetID;
                            self.selectedFilteredTimeseries = self.filtered_timeseries[0].id;
                        } catch (e) {

                        }
                        self.defaultOption.filtered_timeseries = "--- Filtered timeseries ---";
                    })
                    .catch(function() {
                        self.defaultOption.filtered_timeseries = "Loading failed";
                    });
            }, this.searchbyname.doneTypingInterval);

        },
        fetchTimeseriesInfo() { // showFirstMapping=true&listCol=true
            this.timeseries_columns = [];
            this.firstMapping = null;
            let url = "../timeseries/?id=" + this.selected.timeseries_id + "&showFirstMapping=true&listCol=true";

            let self = this;
            axios
                .get(url)
                .then(response => {
                    if (response.data.data.length > 0) {
                        try {
                            self.timeseries_columns = response.data.data[0].columns;
                            self.firstMapping = response.data.data[0].firstMapping;
                            self.period.firstAvailable = response.data.data[0].first_time ? response.data.data[0].first_time : "unknown";
                            self.period.lastAvailable = response.data.data[0].last_time ? response.data.data[0].last_time : "unknown";
                        } catch (e) {}
                        self.selectedTimeseriesColumns = self.timeseries_columns;
                    }
                })
                .catch(function() {
                    self.timeseries_columns = ["No columns"];
                });
        },
        onSubmit(event) {
            event.preventDefault();
        },
        onRemove(event) {
            event.preventDefault();
            this.request.request_id = null;
        },
        onReset(event) {
            event.preventDefault()
            this.selected.net_id = this.resetID;
            this.initialFetch();
            this.initForm();
        },
        onStationMapMarkerClick(id) {
            this.selected.station_id = id;
        }
    },
    watch: {
        selected: {
            handler: function(v) {
                console.log(v);
                if (v.net_id != this.previous_selected.net_id) {
                    this.previous_selected.net_id = v.net_id;
                    this.fetchStations();
                }
                if (v.station_id != this.previous_selected.station_id) {
                    this.previous_selected.station_id = v.station_id;
                    this.fetchStationConfigs();
                }
                if (v.station_config_id != this.previous_selected.station_config_id) {
                    this.previous_selected.station_config_id = v.station_config_id;
                    this.fetchChannels();
                }
                if (v.channel_id != this.previous_selected.channel_id) {
                    this.previous_selected.channel_id = v.channel_id;
                    this.fetchTimeseries();
                }
                if (v.timeseries_id != this.previous_selected.timeseries_id) {
                    this.previous_selected.timeseries_id = v.timeseries_id;
                    this.request.id = v.timeseries_id;
                    this.fetchTimeseriesInfo(v.timeseries_id);
                }
            },
            deep: true
        },
        searchbyname_active: {
            handler: function(value) {
                if (value) {
                    this.fetchFilteredTimeseries(this.filterTimeseriesName);
                }
            },
            deep: true
        },
        selectedFilteredTimeseries: {
            handler: function(value) {
                this.selected.timeseries_id = value;
            },
            deep: true
        },
        firstMapping: {
            handler: function(value) {
                if (this.searchbyname_active) {
                    let self = this;
                    Object.keys(value).forEach(function(key) {
                        if (value[key] === null) value[key] = self.resetID;
                    });
                    self.selected = value;
                }
            },
            deep: true
        },
        selectedTimeseriesColumns: {
            handler: function(array) {
                this.request.columns = array ? array : []; // avoid {"columns" : null}
            }
        },
        filtered_timeseries: {
            handler: function(value) {
                console.log(value);
            }
        },
        aggregation: {
            handler: function(v) {
                if (this.alertMinTimeWindow) {
                    this.aggregation.selectedTimeTypeCount = 60;
                }
                this.request.aggregate = v.selectedFunction;
                this.request.time_bucket = v.selectedTimeTypeCount + " " + v.selectedTimeType + (v.selectedTimeTypeCount > 1 ? "s" : "");
                if (this.aggregation.disabled) {
                    delete this.request.aggregate;
                    delete this.request.time_bucket;
                }
            },
            deep: true
        },
        period: {
            handler: function(v) {

                this.period.start.isValidDate = (v.start.DateSelected != "");
                this.period.start.isValidTime = this.validateHhMm(v.start.Time);
                this.period.end.isValidDate = (v.end.DateSelected != "");
                this.period.end.isValidTime = this.validateHhMm(v.end.Time);
                if (this.period.start.isValidDate && this.period.start.isValidTime) {
                    this.request.starttime = v.start.DateSelected + " " + (v.start.Time.padEnd(8, ':00'));
                } else {
                    delete this.request.starttime;
                }
                if (this.period.end.isValidDate && this.period.end.isValidTime) {
                    this.request.endtime = v.end.DateSelected + " " + (v.end.Time.padEnd(8, ':00'));
                } else {
                    delete this.request.endtime;
                }

            },
            deep: true
        }
    }
};
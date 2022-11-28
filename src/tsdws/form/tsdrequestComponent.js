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
            sensors: [],
            channels: [],
            timeseries: [],
            timeseries_columns: [],
            selectedTimeseriesColumns: [],
            filterTimeseriesName: '',
            filtered_timeseries: [],
            selectedFilteredTimeseries: 0,
            previous_selected: {
                net_id: 0,
                sensor_id: 0,
                channel_id: 0,
                timeseries_id: 0
            },
            selected: {
                net_id: 0,
                sensor_id: 0,
                channel_id: 0,
                timeseries_id: 0
            },
            firstMapping: {
                net_id: 0,
                sensor_id: 0,
                channel_id: 0,
                timeseries_id: 0
            },
            defaultOption: {
                nets: "--- Select net ---",
                sensors: "--- Select sensor ---",
                channels: "--- Select channel ---",
                timeseries: "--- Select timeseries ---",
                filtered_timeseries: "--- Filtered timeseries ---"
            },
            aggregation: {
                functions: ["AVG", "MEDIAN", "COUNT", "MAX", "MIN", "SUM"],
                selectedFunction: null,
                timeTypes: ["second", "minute", "hour", "day", "hour", "year"],
                selectedTimeType: null,
                selectedTimeTypeCount: null
            },
            period: {
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
            this.filterTimeseriesName = '';
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
        fetchSensors() {
            this.defaultOption.sensors = "Loading...";
            this.sensors = [];

            let self = this;
            axios
                .get("../sensors/?net_id=" + self.selected.net_id)
                .then(response => {
                    self.sensors = response.data.data;
                    self.defaultOption.sensors = "--- Select sensor ---";
                    try {
                        if (self.searchbyname_active) {
                            self.selected.sensor_id = (self.firstMapping.sensor_id && self.firstMapping.sensor_id !== null) ? self.firstMapping.sensor_id : self.resetID;
                        } else {
                            self.selected.sensor_id = self.sensors[0].id;
                        }
                    } catch (e) {
                        self.selected.sensor_id = self.resetID;
                    }
                    self.$refs.stationMap.plotOnMap(self.sensors, self.selected.sensor_id);
                })
                .catch(function() {
                    self.defaultOption.sensors = "Loading failed";
                });
        },
        fetchChannels() {
            this.defaultOption.channels = "Loading...";
            this.channels = [];

            let self = this;
            axios
                .get("../channels/?sensor_id=" + self.selected.sensor_id)
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
            let url = "../timeseries/?listCol=true&channel_id=" + this.selected.channel_id;

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
                let url = "../timeseries/?showFirstMapping=true&listCol=true&name=" + name;

                axios
                    .get(url)
                    .then(response => {
                        self.filtered_timeseries = response.data.data;
                        self.selectedFilteredTimeseries = self.resetID;
                        try {
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
        set_timeseries_columns(timeseries_array) {
            let self = this;
            let record = timeseries_array.filter(function(item) {
                return (item.id == self.selected.timeseries_id);
            });
            this.timeseries_columns = [];
            try {
                this.timeseries_columns = record[0].columns;
            } catch (e) {}
            this.selectedTimeseriesColumns = this.timeseries_columns;
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
            this.selected.sensor_id = id;
        }
    },
    watch: {
        selected: {
            handler: function(v) {
                console.log(v);
                if (v.net_id != this.previous_selected.net_id) {
                    this.previous_selected.net_id = v.net_id;
                    this.fetchSensors();
                }
                if (v.sensor_id != this.previous_selected.sensor_id) {
                    this.previous_selected.sensor_id = v.sensor_id;
                    this.fetchChannels();
                }
                if (v.channel_id != this.previous_selected.channel_id) {
                    this.previous_selected.channel_id = v.channel_id;
                    this.fetchTimeseries();
                }
                if (v.timeseries_id != this.previous_selected.timeseries_id) {
                    this.previous_selected.timeseries_id = v.timeseries_id;
                    this.request.id = v.timeseries_id;
                    this.set_timeseries_columns(this.searchbyname_active ? this.filtered_timeseries : this.timeseries);
                }

            },
            deep: true
        },
        searchbyname: {
            handler: function(value) {
                if (!value.disabled) {
                    this.filterTimeseriesName = '' + this.filterTimeseriesName;
                }
            },
            deep: true
        },
        selectedFilteredTimeseries: {
            handler: function(value) {
                let f = this.filtered_timeseries.filter(function(item) {
                    return (item.id == value);
                });
                this.firstMapping = f[0].firstMapping;
            },
            deep: true
        },
        firstMapping: {
            handler: function() {
                if (this.searchbyname_active) {
                    let self = this;
                    Object.keys(self.firstMapping).forEach(function(key) {
                        if (self.firstMapping[key] === null) self.firstMapping[key] = self.resetID;
                    });
                    self.selected = self.firstMapping;
                }
            },
            deep: true
        },
        selectedTimeseriesColumns: {
            handler: function(array) {
                this.request.columns = array ? array : []; // avoid {"columns" : null}
            }
        },
        filterTimeseriesName: {
            handler: function(value) {
                this.fetchFilteredTimeseries(value);
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
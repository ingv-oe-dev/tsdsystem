const tsdformRequestComponentDefinition = {
    props: ["request"],
    data() {
        return {
            searchbyname: {
                value: false,
                options: [
                    { text: 'PNet', value: false },
                    { text: 'Name', value: true },
                ],
                typingTimer: null,
                doneTypingInterval: 3000
            },
            resetID: 0,
            nets: [],
            selectedNet: 0,
            sensors: [],
            selectedSensor: 0,
            channels: [],
            selectedChannel: 0,
            timeseries: [],
            selectedTimeseries: 0,
            timeseries_columns: [],
            selectedTimeseriesColumns: [],
            filterTimeseriesName: '',
            filtered_timeseries: [],
            defaultOption: {
                nets: "--- Select net ---",
                sensors: "--- Select sensor ---",
                channels: "--- Select channel ---",
                timeseries: "--- Select timeseries ---"
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
            return this.searchbyname.value
        },
        alertMinTimeWindow() {
            return this.aggregation.selectedTimeTypeCount < 60 && this.aggregation.selectedTimeType == "second";
        }
    },
    methods: {
        initialFetch() {
            this.filterTimeseriesName = '';
            this.searchbyname_active ? this.fetchTimeseries(this.filterTimeseriesName) : this.fetchNets();
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
                .get("/tsdws/nets")
                .then(response => {
                    self.nets = response.data.data;
                    self.selectedNet = self.resetID;
                    try {
                        self.selectedNet = self.nets[0].id
                    } catch (e) {}
                    self.defaultOption.nets = "--- Select net ---";
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
                .get("/tsdws/sensors/?net_id=" + self.selectedNet)
                .then(response => {
                    self.sensors = response.data.data;
                    self.selectedSensor = self.resetID;
                    try {
                        self.selectedSensor = self.sensors[0].id;
                    } catch (e) {}
                    self.defaultOption.sensors = "--- Select sensor ---";
                    self.$refs.stationMap.plotOnMap(self.sensors, self.selectedSensor);
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
                .get("/tsdws/channels/?sensor_id=" + self.selectedSensor)
                .then(response => {
                    self.channels = response.data.data;
                    self.selectedChannel = self.resetID;
                    try {
                        self.selectedChannel = self.channels[0].id;
                    } catch (e) {}
                    self.defaultOption.channels = "--- Select channel ---";
                })
                .catch(function() {
                    self.defaultOption.channels = "Loading failed";
                });
        },
        fetchTimeseries(name) {
            this.defaultOption.timeseries = "Loading...";
            this.timeseries = [];
            this.filtered_timeseries = [];
            let url = "/tsdws/timeseries/?showColDefs=true" + ((name === undefined) ? ("&channel_id=" + this.selectedChannel) : ("&name=" + name));

            let self = this;
            axios
                .get(url)
                .then(response => {
                    self.timeseries = response.data.data;
                    self.filtered_timeseries = response.data.data;
                    self.selectedTimeseries = self.resetID;
                    if (name === undefined) {
                        try {
                            self.selectedTimeseries = self.timeseries[0].id;
                        } catch (e) {}
                    }
                    self.defaultOption.timeseries = "--- Select timeseries ---";
                })
                .catch(function() {
                    self.defaultOption.timeseries = "Loading failed";
                });
        },
        set_timeseries_columns(timeseries_array) {
            let self = this;
            let record = timeseries_array.filter(function(item) {
                return (item.id == self.selectedTimeseries);
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
                // Reset our form values
            if (this.searchbyname_active) {
                this.filterTimeseriesName = '';
            } else {
                this.selectedNet = this.resetID;
                this.initialFetch();
            }
            this.initForm();
        },
        onStationMapMarkerClick(id) {
            this.selectedSensor = id;
        }
    },
    watch: {
        selectedNet: {
            handler: function(r) {
                //console.log(r);
                this.fetchSensors();
            },
            deep: true
        },
        selectedSensor: {
            handler: function(r) {
                //console.log(r);
                this.fetchChannels();
            },
            deep: true
        },
        selectedChannel: {
            handler: function(r) {
                //console.log(r);
                this.fetchTimeseries();
            },
            deep: true
        },
        selectedTimeseries: {
            handler: function(r) {
                this.request.timeseries_id = r;
                this.set_timeseries_columns(this.searchbyname_active ? this.filtered_timeseries : this.timeseries);
            }
        },
        selectedTimeseriesColumns: {
            handler: function(array) {
                this.request.columns = array ? array : []; // avoid {"columns" : null}
            }
        },
        searchbyname_active: {
            handler: function() {
                this.initialFetch();
            }
        },
        filterTimeseriesName: {
            handler: function(value) {
                this.filtered_timeseries = this.timeseries.filter(function(item) {
                    return (item.name.toLowerCase().indexOf(value.toLowerCase()) >= 0);
                });
                this.selectedTimeseries = this.resetID;
                try {
                    this.selectedTimeseries = this.filtered_timeseries[0].id;
                } catch (e) {}
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
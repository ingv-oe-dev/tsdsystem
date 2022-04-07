const plotlyChartComponentDefinition = {
    props: ["chart"],
    data: function() {
        return {
            id: null,
            selectedTraceIndex: 0,
            settings: false,
            _chart_config: null,
            loaded: 0,
            tsLoadingErrorResult: '',
            newData: {
                date: null,
                time: null,
                value: null
            }
        }
    },
    created() {
        let vueself = this;
        vueself._chart_config = {
            responsive: true,
            //staticPlot: true,
            editable: true,
            /*toImageButtonOptions: {
            	format: 'svg', // one of png, svg, jpeg, webp
            	filename: 'custom_image',
            	height: 500,
            	width: 700,
            	scale: 1 // Multiply title/legend/axis/canvas sizes by this factor
            },*/
            modeBarButtonsToAdd: [{
                name: 'showSettings',
                icon: Plotly.Icons.pencil,
                direction: 'up',
                click: function(gd) {
                    //console.log(vueself);
                    vueself.settings = !vueself.settings;
                }
            }, {
                name: 'downloadCSV',
                icon: Plotly.Icons.disk,
                direction: 'up',
                click: function(gd) {
                    console.log(gd);
                    var text = '';
                    for (var i = 0; i < gd.data.length; i++) {
                        text += gd.data[i].name + '\n';
                        text += gd.layout.xaxis.title.text + "," + gd.layout.yaxis.title.text + '\n';
                        for (var j = 0; j < gd.data[i].x.length; j++) {
                            text += gd.data[i].x[j] + "," + gd.data[i].y[j] + '\n';
                        }
                        text += '\n';
                    };
                    var blob = new Blob([text], { type: 'text/plain' });
                    var a = document.createElement('a');
                    const object_URL = URL.createObjectURL(blob);
                    a.href = object_URL;
                    a.download = 'data.csv';
                    document.body.appendChild(a);
                    a.click();
                    URL.revokeObjectURL(object_URL);
                }
            }]
        }
    },
    mounted() {
        //console.log(this.chart);
        this.id = this.chart.uuid;
        this.chart.traces.forEach(this.loadData);
    },
    methods: {
        selectTrace: function() {
            console.log(this.selectedTraceIndex);
        },
        loadData: function(element, index) {
            let vueself = this;
            let wsURL = 'proxy-request.php';
            /*
            axios.post(wsURL, element.request)
                .then(function(response) {
                    element.x = response.data.data.timestamp;
                    element.y = response.data.data[element.request.columns[0]];
                })
                .catch(function(error) {
                    vueself.tsLoadingErrorResult = error;
                })
                .then(function() {
                    // always executed
                    vueself.loaded++;
                });
            */
            $.ajax({
                url: wsURL,
                data: element.request,
                type: 'POST',
                success: function(response) {
                    element.x = response.data.data.timestamp;
                    element.y = response.data.data[element.request.columns[0]];
                },
                error: function(data) {
                    vueself.tsLoadingErrorResult = data.responseJSON["error"];
                },
                complete: function() {
                    vueself.loaded++;
                }
            });

        },
        newRandomData: function() {
            let d = new Date();
            let v = Math.random();

            this.newData.date = `${d.getUTCFullYear()}-${pad(d.getUTCMonth() + 1)}-${pad(d.getUTCDate())}`;
            this.newData.time = `${pad(d.getUTCHours())}:${pad(d.getUTCMinutes())}:${pad(d.getUTCSeconds())}`;
            this.newData.value = v;
        },
        addData: function(chart, index) {

            let x = `${this.newData.date} ${this.newData.time}`;
            let y = this.newData.value;

            chart.traces[index].x.push(x);
            chart.traces[index].y.push(y);

            chart.layout.datarevision = new Date().getTime(); //force update - do not remove!
        },
        relayout: function(yAxisTitle) {
            // Force relayout on change yAxis type (linear - log)
            this.chart.layout[yAxisTitle].type = this.chart.layout[yAxisTitle].type;
            Plotly.react(this.$refs[this.chart.uuid], this.chart.traces, this.chart.layout, this._chart_config);
        }
    },
    computed: {
        // a computed getter
        loading: function() {
            // `this` points to the vm instance
            return this.chart.traces.length > this.loaded
        },
        yAxisList: function() {
            return Object.keys(this.chart.layout).filter(v => /^yaxis/.test(v));
        },
        isBarTypeTrace: function() {
            return this.chart.traces[this.selectedTraceIndex].type == 'bar';
        },
        isMarkerOnlyTrace: function() {
            return this.chart.traces[this.selectedTraceIndex].mode == "markers";
        },
        isLineOnlyTrace: function() {
            return this.chart.traces[this.selectedTraceIndex].mode == "lines";
        }
    },
    watch: {
        chart: {
            handler: function() {
                //console.log('react');
                Plotly.react(this.$refs[this.chart.uuid], this.chart.traces, this.chart.layout, this._chart_config);
            },
            deep: true
        },
        newData: {
            handler: function(v) {
                //console.log(v)
            },
            deep: true
        }
    }
};
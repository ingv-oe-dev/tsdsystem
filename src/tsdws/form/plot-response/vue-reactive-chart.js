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
            },
            showThresholds: true
        }
    },
    created() {
        let vueself = this;
        vueself._chart_config = {
            responsive: true,
            //staticPlot: true,
            //editable: true, // removed to avoid shapes editing
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
                    // console.log(gd);
                    // prepare time array
                    var x_array = [];
                    if (gd.data.length > 0) {
                        for (var k = 0; k < gd.data[0].x.length; k++) {
                            x_array.push(gd.data[0].x[k]);
                        }
                    }
                    // prepare headers
                    var text = gd.layout.xaxis.title.text;
                    for (var i = 0; i < gd.data.length; i++) {
                        text += "," + gd.layout["yaxis" + (i==0?"":(i+1))].title.text;
                    };
                    text += '\n';
                    // compile body
                    for (var k = 0; k < x_array.length; k++) {
                        text += x_array[k];
                        for (var i = 0; i < gd.data.length; i++) {
                            text += "," + gd.data[i].y[k];
                        }
                        text += '\n';
                    }
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
            wsURL = "/tsdws/timeseries/" + element.request.id + "/values";

            $.ajax({
                url: wsURL,
                data: element.request,
                type: 'GET',
                success: function(response) {
                    //console.log(response);
                    element.x = response.data.timestamp;
                    element.y = response.data[element.request.columns[0]];
                    //console.log(element);

                    // update column's label with measure unit returned from database
                    let label = "yaxis" + (index == 0 ? "" : index + 1);
                    try {
                        let columns_info = response.additional_info.metadata.columns;
                        for (let i = 0; i < columns_info.length; i++) {
                            if (columns_info[i].name == element.name) {
                                let mu = columns_info[i].unit ? columns_info[i].unit : "A.U.";
                                vueself.chart.layout[label].title.text += " (" + mu + ")";

                                // plot options
                                let plot_options = columns_info[i].plot_options;
                                if (plot_options) {

                                    if (plot_options.axis_type || element.request.axis_type) {
                                        let axis_type = plot_options.axis_type ? plot_options.axis_type : element.request.axis_type;
                                        vueself.chart.layout[label].type = axis_type;
                                    }

                                    if (plot_options.type || element.request.type) {
                                        let type = plot_options.type ? plot_options.type : element.request.type;
                                        element.type = type;
                                    }

                                    if (plot_options.mode || element.request.mode) {
                                        let mode = plot_options.mode ? plot_options.mode : element.request.mode;
                                        element.mode = mode;
                                    }

                                    if (plot_options.color || element.request.color) {
                                        let color = plot_options.color ? plot_options.color : element.request.color;
                                        element.line.color = color;
                                        element.marker.color = color;
                                    }
                                }

                                // thresholds
                                yrange = vueself.chart.layout[label].type == "log" ? [-Math.inf, Math.inf] : [-(Math.min.apply(null, element.y)), Math.max.apply(null, element.y)]
                                vueself.plotThresholds(
                                    thresholds = columns_info[i].thresholds,
                                    yrange = yrange,
                                    hide = !element.request.showThresholds
                                );
                            }
                        }
                    } catch (e) {}
                },
                error: function(data) {
                    vueself.tsLoadingErrorResult = data.responseJSON["error"];
                },
                complete: function() {
                    vueself.loaded++;
                }
            });

        },
        plotThresholds: function(thresholds, yrange, hide) {
            //console.log(thresholds, yrange);
            if (thresholds && Array.isArray(thresholds)) {

                this.chart.layout.shapes = [];

                for (let i = 0; i < thresholds.length; i++) {

                    if (thresholds[i].from_t) {
                        this.chart.layout.shapes.push({
                            type: 'line',
                            xref: 'paper',
                            x0: 0,
                            y0: thresholds[i].from_t,
                            x1: 1,
                            y1: thresholds[i].from_t,
                            line: {
                                //color: '#000',
                                width: 0,
                                //dash: 'dot'
                            },
                            layer: 'below'
                        });
                    }

                    if (thresholds[i].from_t && thresholds[i].to_t) {
                        this.chart.layout.shapes.push({
                            type: 'rect',
                            xref: 'paper',
                            x0: 0,
                            y0: thresholds[i].from_t,
                            x1: 1,
                            y1: thresholds[i].to_t,
                            fillcolor: thresholds[i].color,
                            opacity: 0.2,
                            line: {
                                width: 0
                            },
                            layer: 'below'
                        });
                    }

                    if (!thresholds[i].from_t && thresholds[i].to_t) {
                        this.chart.layout.shapes.push({
                            type: 'rect',
                            xref: 'paper',
                            x0: 0,
                            y0: yrange[0],
                            x1: 1,
                            y1: thresholds[i].to_t,
                            fillcolor: thresholds[i].color,
                            opacity: 0.2,
                            line: {
                                width: 0
                            },
                            layer: 'below'
                        });
                    }

                    if (thresholds[i].from_t && !thresholds[i].to_t) {
                        this.chart.layout.shapes.push({
                            type: 'rect',
                            xref: 'paper',
                            x0: 0,
                            y0: thresholds[i].from_t,
                            x1: 1,
                            y1: yrange[1],
                            fillcolor: thresholds[i].color,
                            opacity: 0.2,
                            line: {
                                width: 0
                            },
                            layer: 'below'
                        });
                    }
                }

                this.relayout();

                if (hide) {
                    this.showThresholds = false;
                }
            }
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
        relayoutAxis: function(yAxisTitle) {
            // Force relayout on change yAxis type (linear - log)
            this.chart.layout[yAxisTitle].type = this.chart.layout[yAxisTitle].type;
            this.relayout();
        },
        relayout: function() {
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
        isScatterTrace: function() {
            return this.chart.traces[this.selectedTraceIndex].type == 'scatter';
        },
        isBarTypeTrace: function() {
            return this.chart.traces[this.selectedTraceIndex].type == 'bar';
        },
        isMarkerOnlyTrace: function() {
            return this.isScatterTrace && this.chart.traces[this.selectedTraceIndex].mode == "markers";
        },
        isLineOnlyTrace: function() {
            return this.isScatterTrace && this.chart.traces[this.selectedTraceIndex].mode == "lines";
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
        },
        showThresholds: {
            handler: function(v) {
                console.log(v)
                for (let i = 0; i < this.chart.layout.shapes.length; i++) {
                    this.chart.layout.shapes[i].visible = v;
                }
                this.relayout()
            },
            deep: true
        }
    }
};
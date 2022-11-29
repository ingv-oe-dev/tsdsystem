var app;
window.onload = function() {

    // register components
    axios
        .get("plotly-chart-template.html")
        .then(response_html => {
            plotlyChartComponentDefinition.template = response_html.data;
            Vue.component("plotly-chart", plotlyChartComponentDefinition);

            // launch app
            app = new Vue({
                el: "#app",
                data() {
                    return {
                        charts: charts
                    };
                },
                mounted() {
                    //console.log(this.charts);
                    var plot_divs = document.getElementsByClassName('plotly-plot');
                    this.charts.forEach(chart => {
                        if (chart.traces.length > 0) {
                            var myPlot = document.getElementById(chart.uuid);
                            Plotly.newPlot(chart.uuid, chart.traces, chart.layout);
                            myPlot.on("plotly_relayout", function(ed) {
                                relayout(ed, plot_divs);
                            });
                        }
                    });
                }
            });
        });
}

function pad(num, size) {
    if (typeof size === "undefined") size = 2;
    var s = new String("000000" + num);
    return s.substring(s.length - size);
}

function relayout(ed, divs) {
    if (Object.entries(ed).length === 0) { return; }
    for (let div of divs) {
        //console.log(div);
        let x = div.layout.xaxis;
        //console.log(ed);
        //console.log(x.range);
        if (ed["xaxis.autorange"] && x.autorange) continue;
        if (x.range[0] != ed["xaxis.range[0]"] || x.range[1] != ed["xaxis.range[1]"]) {
            var update = {
                'xaxis.range[0]': ed["xaxis.range[0]"],
                'xaxis.range[1]': ed["xaxis.range[1]"],
                'xaxis.autorange': ed["xaxis.autorange"]
            };
            Plotly.relayout(div, update);
        }
    }
}


// Example of adding new data to first trace (index=0) of first chart (with uuid="aferefzyrt"):
// app.$children[0].id = chart.uuid (see on mount function of vue-reactive-chart template)
/*
var chart_component = app.$children.find(child => { return child.id === "aferefzyrt"; });
chart_component.newData = {date: "2020-10-29", time: "19:00:00", value:6};
chart_component.addData(chart_component.chart,0);
*/
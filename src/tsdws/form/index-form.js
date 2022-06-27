var app;

axios
    .get("tsdrequestComponent.html")
    .then(response_html => {
        tsdformRequestComponentDefinition.template = response_html.data;
        Vue.component("tsdformrequest", tsdformRequestComponentDefinition);
        Vue.component("stationmap", stationMapComponentDefinition);

        // launch app
        app = new Vue({
            el: "#app",
            data() {
                return {
                    counter: 0,
                    currentUUID: null,
                    emptyRequest: {
                        id: 0
                    },
                    requests: [],
                    period: {
                        setForAllRequests: true,
                        nDays: 7,
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
                    }
                };
            },
            mounted() {
                // set default request period
                this.initPeriod();
            },
            computed: {
                n_requests: function() {
                    let filtered = this.aliveRequests();
                    return filtered.length
                },
                next_counter: function() {
                    return Math.max(this.n_requests, this.counter++);
                }
            },
            methods: {
                initPeriod: function() {

                    let now = new Date();
                    let endtime = this.ISODateString(now);
                    let dateOffset = (24 * 60 * 60 * 1000) * this.period.nDays;
                    let before = new Date(now.getTime() - dateOffset);
                    let starttime = this.ISODateString(before);

                    this.period.start.Date = starttime.substring(0, 10);
                    this.period.start.Time = starttime.substring(11, 19);
                    this.period.end.Date = endtime.substring(0, 10);
                    this.period.end.Time = endtime.substring(11, 19);
                },
                // get all undeleted requests (with request_id <> null)
                aliveRequests: function() {
                    return this.requests.filter(function(item) {
                        return item.request_id !== null;
                    });
                },
                // update requests list forcing delete of elements with request_id = null
                flushRequests: function() {
                    this.requests = this.aliveRequests();
                },
                // add a new request
                addRequest: function() {

                    this.flushRequests();

                    this.currentUUID = this.uuidv4();

                    // generate request
                    let new_request = this.generateRequest();

                    // add request to list
                    this.requests.push(new_request);
                },
                // Remove all requests
                removeAllRequests: function() {
                    this.requests = [];
                    this.counter = 0;
                    this.currentUUID = null;
                },
                sendRequests: function() {
                    let form = document.createElement('form');
                    form.method = 'POST';
                    form.action = "plot-response/";
                    form.target = '_blank';
                    let input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'requests';
                    input.value = JSON.stringify(this.requests);
                    form.appendChild(input);
                    document.body.appendChild(form);
                    form.submit();
                },
                // generate a new empty instance of request
                generateRequest: function() {
                    return Object.assign({
                        request_id: this.currentUUID,
                        title: " Untitled request - " + (this.next_counter),
                        starttime: this.period.start.Date + " " + this.period.start.Time,
                        endtime: this.period.end.Date + " " + this.period.end.Time
                    }, this.emptyRequest); // make a deep copy of an empty request from template
                },
                // generate a request_id for a request
                uuidv4: function() {
                    return ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, c =>
                        (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16)
                    );
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
                ISODateString(d) {
                    function pad(n) { return n < 10 ? '0' + n : n }
                    return d.getUTCFullYear() + '-' +
                        pad(d.getUTCMonth() + 1) + '-' +
                        pad(d.getUTCDate()) + ' ' +
                        pad(d.getUTCHours()) + ':' +
                        pad(d.getUTCMinutes()) + ':' +
                        pad(d.getUTCSeconds())
                }
            },
            watch: {
                requests: {
                    handler: function(list) {
                        //console.log(list)
                    },
                    deep: true
                },
                period: {
                    handler: function(v) {
                        this.period.start.isValidDate = (v.start.DateSelected != "");
                        this.period.start.isValidTime = this.validateHhMm(v.start.Time);
                        this.period.end.isValidDate = (v.end.DateSelected != "");
                        this.period.end.isValidTime = this.validateHhMm(v.end.Time);
                    },
                    deep: true
                }
            }
        });
    });
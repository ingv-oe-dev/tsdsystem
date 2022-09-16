const notificationListComponentDefinition = {
    template: `<div class="">
    <button class="bg-secondary text-light float-end" style="border:none" type="button" @click="readAll()">
      <div style="font-size:0.9em" class='overflow:auto'><i class="fa fa-check"></i> Read all</div>
    </button>
  </div>
  <div class="accordion" id="notificationList">
    <div class="text-light border-bottom border-secondary bg-dark" v-for="notify in list">
      <h2 class="accordion-header" :id="'panelsStayOpen-heading' + notify.id">
        <button :class="{'bg-dark':notify.messageRead, 'text-light':notify.messageRead, 'alert-warning':!notify.messageRead, 'text-dark':!notify.messageRead}" class="accordion-button p-1 ps-2" type="button" data-bs-toggle="collapse" :data-bs-target="'#panelsStayOpen-collapse' + notify.id" aria-expanded="true" :aria-controls="'panelsStayOpen-collapse' + notify.id" @click="notify.messageRead=true">
          <div style="font-size:0.9em" class='font-monospace overflow:auto' :class="'text-'+notify.messageType"> <i v-if="notify.messageType=='danger'" class="fa fa-triangle-exclamation"></i> <i v-if="notify.messageType=='warning'" class="fa fa-circle-exclamation"></i> <i v-if="notify.messageType=='success'" class="fa fa-circle-check"></i> <i v-if="notify.messageType=='info'" class="fa fa-circle-info"></i> <span class="fst-italic">{{notify.messageText}}</span> - <span>{{notify.type}}</span> <span :class="{'text-light':notify.messageRead, 'text-dark':!notify.messageRead}">{{notify.url}}</span> <span>{{notify.status}}</span> <span>{{notify.statusText}}</span></div>
        </button>
      </h2>
      <div :id="'panelsStayOpen-collapse' + notify.id" class="accordion-collapse collapse" :aria-labelledby="'panelsStayOpen-heading' + notify.id">
        <div class="accordion-body" style="font-size:0.9em">
          <div class='fw-bold text-warning'>{{notify.responseJSON.error}}</div>
          <pre class='text-info' style='white-space: pre-wrap; word-break: break-word;'>{{JSON.stringify(notify, null, 4)}}</pre>
        </div>
      </div>
    </div>
  </div>`,
    data() {
        return {
            list: {},
            defaultNotify: {
                "id": null,
                "messageText": "",
                "messageType": "",
                "messageRead": false
            }
        }
    },
    mounted() {},
    computed: {},
    methods: {
        notify(n) {
            //console.log(n);
            var length = Object.keys(this.list).length;
            var item = Object.assign({}, this.defaultNotify, n, { "id": length });
            //console.log(item);
            this.list[length] = item;
        },
        readAll() {
            for (n in this.list) {
                this.list[n].messageRead = true;
            }
        }
    },
    watch: {
        list: {
            handler(val) {
                //console.log(val);
            },
            deep: true
        }
    },
    computed: {
        toRead() {
            let counter = 0;
            for (n in this.list) {
                this.list[n].messageRead ? null : counter++;
            }
            return counter;
        }
    }
};
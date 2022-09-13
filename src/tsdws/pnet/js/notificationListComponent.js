const notificationListComponentDefinition = {
    template: `<div class="accordion" id="notificationList">
    <div class="accordion-item text-light border-bottom border-secondary" style='background-color:#000' v-for="notify in list">
      <h2 class="accordion-header" :id="'panelsStayOpen-heading' + notify.id">
        <button :class="{'bg-dark':notify.messageRead, 'text-light':notify.messageRead, 'bg-warning':!notify.messageRead, 'text-dark':!notify.messageRead}" class="accordion-button p-1 ps-2" type="button" data-bs-toggle="collapse" :data-bs-target="'#panelsStayOpen-collapse' + notify.id" aria-expanded="true" :aria-controls="'panelsStayOpen-collapse' + notify.id" @click="notify.messageRead=true">
          <div style="font-size:0.9em" class='font-monospace overflow:auto'><span :class="{'text-danger':notify.messageType=='danger'}">{{notify.type}}</span> {{notify.url}} <span :class="{'text-danger':notify.messageType=='danger'}">{{notify.status}}</span> <span :class="{'text-danger':notify.messageType=='danger'}">{{notify.statusText}}</span></div>
        </button>
      </h2>
      <div :id="'panelsStayOpen-collapse' + notify.id" class="accordion-collapse collapse" :aria-labelledby="'panelsStayOpen-heading' + notify.id">
        <div class="accordion-body" style="font-size:0.9em">
          <div :class="{'text-danger':notify.messageType=='danger'}">{{notify.responseJSON.error}}</div>
          <pre class='text-info' style='white-space: pre-wrap; word-break: break-word;'>{{JSON.stringify(notify, null, 4)}}</pre>
        </div>
      </div>
    </div>
  </div>`,
    data() {
        return {
            list: {}
        }
    },
    mounted() {},
    computed: {},
    methods: {
        notify(n, messageType) {
            //console.log(n);
            var length = Object.keys(this.list).length;
            var obj = Object.assign(n, { "id": length, "messageRead": false, "messageType": messageType });
            this.list[length] = obj;
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
            for (var i = 0; i < Object.keys(this.list).length; i++) {
                if (!this.notifications[Object.keys(this.list)[i]].messageRead) {
                    counter++;
                }
            }
            return counter;
        }
    }
};
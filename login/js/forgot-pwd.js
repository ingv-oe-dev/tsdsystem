// launch app
var app = new Vue({
    el: "#app",
    data() {
        return {
            email: '',
            errorLogin: '',
            successLogin: ''
        }
    },
    computed: {
        validateEmail: function() {
            return (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(this.email));
        },
        warningEmail: function() {
            return this.validateEmail ? '' : 'Please enter a valid email address';
        }
    },
    methods: {
        sendMail(event) {
            let self = this;
            event.preventDefault();
            if (this.validateEmail) {
                $.ajax({
                    url: "reset_send_mail.php",
                    data: {
                        email: this.email
                    },
                    beforeSend: function() {
                        self.successLogin = '';
                        self.errorLogin = '';
                    },
                    success: function(response) {
                        if (response["status"]) {
                            self.successLogin = response["message"] +
                                ". A reset password email was sent to " + self.email + ".";
                        } else {
                            self.errorLogin = response["error"];
                        }
                    },
                    error: function(error) {
                        self.errorLogin = error;
                    }
                });
            }
        }
    }
});
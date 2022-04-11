// launch app
var app = new Vue({
    el: "#app",
    data() {
        return {
            email: '',
            password: '',
            password2: '',
            showPassword: false,
            errorLogin: '',
            successLogin: ''
        }
    },
    computed: {
        passwordInputType: function() {
            return this.showPassword ? "text" : "password";
        },
        validateEmail: function() {
            return (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(this.email));
        },
        warningEmail: function() {
            return this.validateEmail ? '' : 'Please enter a valid email address';
        },
        validatePassword: function() {
            return (this.password !== '');
        },
        warningPassword: function() {
            return this.validatePassword ? '' : 'Required';
        },
        validatePassword2: function() {
            return (this.password2 !== '');
        },
        warningPassword2: function() {
            return this.validatePassword2 ? (this.samePassword ? '' : 'The passwords are not the same') : 'Required';
        },
        samePassword: function() {
            return (this.password == this.password2);
        },
        validateForm: function() {
            return this.validateEmail && this.validatePassword && this.validatePassword2 && this.samePassword;
        },
        showWelcomeLink: function() {
            return this.successLogin !== '';
        }
    },
    methods: {
        signup(event) {
            let self = this;
            event.preventDefault();
            if (this.validateForm) {
                $.ajax({
                    url: "registration.php",
                    type: "POST",
                    beforeSend: function() {
                        self.successLogin = '';
                        self.errorLogin = '';
                    },
                    data: {
                        email: this.email,
                        password: this.password
                    },
                    success: function(response) {
                        if (response["status"]) {
                            self.successLogin = response["message"] +
                                ". A message was sent to your registration email address (" + self.email + ").";
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
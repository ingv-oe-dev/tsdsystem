// launch app
var app = new Vue({
    el: "#app",
    data() {
        return {
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
            return this.validatePassword && this.validatePassword2 && this.samePassword;
        },
        showWelcomeLink: function() {
            return this.successLogin !== '';
        }
    },
    methods: {
        resetPassword(event) {
            let self = this;
            event.preventDefault();
            if (this.validateForm) {
                $.ajax({
                    url: "reset-pwd-action.php",
                    type: "POST",
                    beforeSend: function() {
                        self.successLogin = '';
                        self.errorLogin = '';
                    },
                    data: {
                        email: $("#email").val(),
                        password: this.password
                    },
                    success: function(response) {
                        if (response["status"]) {
                            self.successLogin = response["message"];
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
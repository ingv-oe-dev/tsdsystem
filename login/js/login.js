// launch app
var app = new Vue({
    el: "#app",
    data() {
        return {
            email: '',
            password: '',
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
        validateForm: function() {
            return this.validateEmail && this.validatePassword;
        }
    },
    methods: {
        signin(event) {
            let self = this;
            event.preventDefault();
            if (this.validateForm) {
                $.ajax({
                    url: "login.php",
                    type: "POST",
                    data: {
                        email: this.email,
                        password: this.password
                    },
                    success: function(response) {
                        if (response.status) {
                            let redirectURL = "welcome.php"
                            if ($("#fromPage").val()) {
                                redirectURL = $("#fromPage").val();
                            }
                            window.location.href = redirectURL;
                        } else {
                            self.errorLogin = response["error"];
                        }
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }
        }
    }
});
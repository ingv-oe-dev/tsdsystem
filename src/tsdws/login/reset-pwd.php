<?php

require_once('..'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'SecureLogin.php');

$sl = new SecureLogin();

$email = $_GET['email'];
$rand_key = $_GET['rand_key'];

$check = $sl->check_valid_reset_password_url($email, $rand_key);

if ($check) {

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>TSDSystem</title>

    <script src="js/jquery-3.6.0.min.js"></script>

    <!-- Load required Bootstrap and BootstrapVue CSS -->
    <link type="text/css" rel="stylesheet" href="../form/js-download/bootstrap.min.css" />
    <link type="text/css" rel="stylesheet" href="../form/js-download/bootstrap-vue.min.css" />
    <link rel="stylesheet" href="../form/js-download/bootstrap-icons.css" />

    <!-- Load Vue followed by BootstrapVue -->
    <script src="../form/js-download/vue.min.js"></script>
    <script src="../form/js-download/vee-validate.js"></script>
    <script src="../form/js-download/bootstrap-vue.min.js"></script>

    <!-- Load the following for BootstrapVueIcons support -->
    <script src="../form/js-download/bootstrap-vue-icons.min.js"></script>
    <!-- MDB -->
    <link rel="stylesheet" href="css/mdb.min.css" />
</head>
<body>
      <!--Main Navigation-->
  <header>
    <style>
      #intro {
        background-image: url(/tsdws/login/ingv.jpeg);
        height: 100vh;
      }
    </style>

    <!-- Background image -->
    <div id="intro" class="bg-image shadow-2-strong">
      <div class="mask d-flex align-items-center h-100" style="background-color: rgba(0, 0, 0, 0.8);">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-xl-5 col-md-8" id='app'>
              <form class="bg-white rounded shadow-5-strong p-5">
				        <h2 class='text-center mb-5'>Reset password</h2>

                <div class='text-muted text-center mb-3'>Account: <?php echo $_GET["email"]; ?></div>
                <input id='email' type='hidden' value="<?php echo $_GET["email"]; ?>" />

                <!-- Password input -->
                <div class="mb-2">
                    <div class="form-outline">
                        <input :type="passwordInputType" v-model="password" class="form-control active"/>
                        <label class="form-label" for="form1Example2">Password</label>
                    </div>
                    <span class='small text-primary text-right'>{{ warningPassword }}</span>
                </div>

                <!-- Password re-input -->
                <div class="mb-1">
                    <div class="form-outline">
                        <input :type="passwordInputType" v-model="password2" class="form-control"/>
                        <label class="form-label" for="form1Example2">Repeat password</label>
                    </div>
                    <span class='small text-primary text-right'>{{ warningPassword2 }}</span>
                </div>

                <div class="form-outline mb-2 text-right custom-control custom-switch b-custom-control-sm">
                    <input type="checkbox" name="checkbox-period" class="custom-control-input" v-model="showPassword" id="__BVID__3">
                    <label class="custom-control-label" for="__BVID__3">Show Passwords</label>
                </div>

                <!-- Submit button -->
                <button class="mb-1 btn btn-primary btn-block" @click="resetPassword" :disabled="!validateForm">Confirm</button>       
                
                <div class="form-group">
                    <p class="text-center text-danger"> {{ errorLogin }}</p>
                    <p class="text-center text-primary"> {{ successLogin }}<br><div v-if='showWelcomeLink'>Return to <a href='welcome.php'>Welcome page</a></div></p>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Background image -->
  </header>
  <!--Footer-->
    <!-- MDB -->
    <script type="text/javascript" src="js/mdb.min.js"></script>
    <!-- Custom scripts -->
    <script type="text/javascript" src="js/reset-pwd.js"></script>
</body>
</html>

<?php
}
else {
  echo "Invalid registration email or invalid key or expired key";
}
?>
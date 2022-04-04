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
				<h2 class='text-center mb-5'>Sign up</h2>

                <input id='fromPage' type='hidden' value="<?php echo isset($_GET["fromPage"]) ? $_GET["fromPage"] : ''; ?>" />

                <!-- Email input -->
                <div class="mb-4">
                    <div class="form-outline">
                        <input type="email" v-model="email" class="form-control active" autofocus/>
                        <label class="form-label" for="form1Example1">Email address</label>
                    </div>
                    <span class='small text-primary text-right'>{{ warningEmail }}</span>
                </div>

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
                <button class="mb-1 btn btn-primary btn-block" @click="signup" :disabled="!validateForm">Sign up</button>       
                
                <div class="form-group">
                    <p class="text-center text-danger"> {{ errorLogin }}</p>
                    <p class="text-center text-primary"> {{ successLogin }}<br><div v-if='showWelcomeLink'>Return to <a href='welcome.php'>welcome page</a></div></p>
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
    <script type="text/javascript" src="js/registration.js"></script>
</body>
</html>
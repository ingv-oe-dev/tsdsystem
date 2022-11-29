<?php
    session_start();
    $email = isset($_SESSION ['email']) ? $_SESSION ['email'] : '';
    $link = isset($_SESSION ['email']) ? "<a href='logout.php'>Logout</a>" : "<a href='.'>Sign in</a>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <title>Login</title>
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
          <div class="row justify-content-center mb-4">
            <div class="col-xl-5 col-md-8">
                <div class='display-4 text-white mb-4'>Welcome into TSDSystem</div>
                <div class='text-white text-italic'>
                  <?php echo $email ?>&nbsp;
                  <span class='badge text-warning text-italic'><?php echo (isset($_SESSION["isAdmin"]) and $_SESSION["isAdmin"]) ? "[admin]" : "" ?></span>
                </div>
                <div><?php echo $link ?></div>
            </div>
          </div>
          <div class="row justify-content-center">
            <div class="col-xl-5 col-md-8">
              <p class='mt-4'><a href='../../swagger/tsdsystem' target='_blank'>Swagger UI</a></p>
              <p class='mt-4'><a href='../pnet'>PNet web app</a></p>
              <?php if ($email != '') { ?>  
                <p class='mt-4'><a href='../form' target='_blank'>Demo Timeseries request</a></p>
              <?php } ?>
              <p class='mt-4'><a href='../form/edit' target='_blank'>Resources edit forms</a></p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Background image -->
  </header>
  <!--Footer-->
</body>
</html>
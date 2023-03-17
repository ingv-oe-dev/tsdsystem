<?php
    session_start();
    $email = isset($_SESSION ['email']) ? $_SESSION ['email'] : '';
    $link = isset($_SESSION ['email']) ? "<a href='logout.php'>Logout</a>" : "<a href='.'>Sign in</a>";
    $isAdmin = (isset($_SESSION["isAdmin"]) and $_SESSION["isAdmin"]);
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
                  <span class='badge text-warning text-italic'><?php echo $isAdmin ? "[admin]" : "" ?></span>
                </div>
                <div><?php echo $link ?></div>
            </div>
          </div>
          <div class="row justify-content-center">
          <div class="col-xl-4 col-md-8">
              <p class='mt-4 text-light'>Swagger UI</p>
              <p class='mt-4'><a href='../../swagger/tsdsystem' target='_blank'>TSDSystem</a></p>
              <p class='mt-4'><a href='../../swagger/fdsn' target='_blank'>FDSN Station XML</a></p>
            </div>
            <?php if ($email != '') { ?>
            <div class="col-xl-4 col-md-8">
              <p class='mt-4 text-light'>Links</p>  
              <p class='mt-3'><a href='../form' target='_blank'>Timeseries request</a> <span class='badge text-success text-italic'>[demo]</span></p>
              <?php if($isAdmin) { ?>
                <p class='mt-3'><a href='../pnet'>PNet web app</a> <span class='badge text-warning text-italic'>[admin]</span></p>
                <p class='mt-3'><a href='../form/edit' target='_blank'>Resources edit forms</a> <span class='badge text-warning text-italic'>[admin]</span></p>
              <?php } ?>
            </div>
            <?php } ?>
            <?php if($isAdmin) { ?>
            <div class="col-xl-4 col-md-8">
              <p class='mt-4 text-light'>Admin Tools <span class='badge text-warning text-italic'>[admin]</span><span class='badge text-info text-italic'>[Available only to full installations]</span></p>
              <p class='mt-4'><a href='../../grafana' target='_blank'>Grafana</a></p>
              <p class='mt-3'><a href='../../pgadmin4' target='_blank'>PGAdmin</a></p>
            </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
    <!-- Background image -->
  </header>
  <!--Footer-->
</body>
</html>
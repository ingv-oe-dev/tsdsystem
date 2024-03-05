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
            <div class="col-xl-3 col-md-8">
              <p class='mt-4 text-light'>Links</p>  
              <p class='mt-3'><a href='https://github.com/ingv-oe-dev/tsdsystem' target='_blank'><svg height="24" aria-hidden="true" viewBox="0 0 16 16" version="1.1" width="32" data-view-component="true" class="octicon octicon-mark-github">
    <path d="M8 0c4.42 0 8 3.58 8 8a8.013 8.013 0 0 1-5.45 7.59c-.4.08-.55-.17-.55-.38 0-.27.01-1.13.01-2.2 0-.75-.25-1.23-.54-1.48 1.78-.2 3.65-.88 3.65-3.95 0-.88-.31-1.59-.82-2.15.08-.2.36-1.02-.08-2.12 0 0-.67-.22-2.2.82-.64-.18-1.32-.27-2-.27-.68 0-1.36.09-2 .27-1.53-1.03-2.2-.82-2.2-.82-.44 1.1-.16 1.92-.08 2.12-.51.56-.82 1.28-.82 2.15 0 3.06 1.86 3.75 3.64 3.95-.23.2-.44.55-.51 1.07-.46.21-1.61.55-2.33-.66-.15-.24-.6-.83-1.23-.82-.67.01-.27.38.01.53.34.19.73.9.82 1.13.16.45.68 1.31 2.69.94 0 .67.01 1.3.01 1.49 0 .21-.15.45-.55.38A7.995 7.995 0 0 1 0 8c0-4.42 3.58-8 8-8Z"></path>
</svg> Download repository</a></p>
            </div>
            <div class="col-xl-3 col-md-8">
              <p class='mt-4 text-light'>Swagger UI</p>
              <p class='mt-4'><a href='../../swagger/tsdsystem' target='_blank'>TSDSystem</a></p>
              <p class='mt-4'><a href='../../swagger/fdsn' target='_blank'>FDSN Station XML</a></p>
            </div>
            <div class="col-xl-3 col-md-8">
              <p class='mt-4 text-light'>Web GUI</p>  
              <?php if ($email != '') { ?>
                <p class='mt-3'><a href='../form' target='_blank'>Timeseries request</a> <span class='badge text-success text-italic'></span></p>
              <?php } ?>
              <p class='mt-3'><a href='../pnet'>PNet web app</a> <span class='badge text-warning text-italic'></span></p>
              <?php if($isAdmin) { ?>
                <p class='mt-3'><a href='../form/edit' target='_blank'>Resources edit forms</a> <span class='badge text-warning text-italic'>[admin]</span></p>
              <?php } ?>
            </div>
            <?php if($isAdmin) { ?>
            <div class="col-xl-3 col-md-8">
              <p class='mt-4 text-light'>Admin Tools <span class='badge text-warning text-italic'>[admin]</span><span class='badge text-info text-italic'>[Available only to full installations]</span></p>
              <p class='mt-4'><a href='../../grafana' target='_blank'>Grafana</a></p>
              <p class='mt-3'><a href='../../pgadmin4' target='_blank'>PGAdmin</a></p>
            </div>
            <?php } ?>
          </div>
          <div class="row justify-content-center mt-4">
            <!-- Footer -->
            <footer class="text-left text-lg-start text-muted" style="background-color:rgba(0,0,0,0.1); font-size: 0.8rem;">
              <!-- Copyright -->
              <div class="text-left p-4">
                <div class="text-light">Copyright© <?php echo date('Y'); ?></div>
                <div>Carmelo Cassisi [carmelo.cassisi@ingv.it], Mario Torrisi [mario.torrisi@ingv.it], Fabrizio Pistagna [fabrizio.pistagna@ingv.it], Marco Aliotta [marco.aliotta@ingv.it], Placido Montalto [placido.montalto@ingv.it]</div>
              </div>
              <!-- Copyright -->
            </footer>
            <!-- Footer -->
          </div>
        </div>
      </div>
    </div>
    <!-- Background image -->
  </header>
</body>
</html>
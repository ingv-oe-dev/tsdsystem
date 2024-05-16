<?php
    $timeseries_id = isset($_GET["timeseries_id"]) ? $_GET["timeseries_id"] : null;
    $origin = isset($_GET["origin"]) ? $_GET["origin"] : null;
    $token = isset($_GET["token"]) ? $_GET["token"] : null;
    //$token = readToken();
    
    /*
    // Read from http header
    function readToken() {
      $token = isset($_SERVER["HTTP_AUTHORIZATION"]) ? $_SERVER["HTTP_AUTHORIZATION"] : NULL;
      if (!is_null($token)) {
        // Handling use of `Bearer` token (even JWT)
        preg_match('/(Bearer[\s]+)*(.+\..+\..+)/', $token, $matches);
        //var_dump($matches);
        $token = $matches[2];
        //var_dump($token);
        return $token;
      }
    }
    */

?>
<!-- HTML for static distribution bundle build -->
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Swagger UI</title>
    <link rel="stylesheet" type="text/css" href="./swagger-ui.css" />
    <link rel="stylesheet" type="text/css" href="index.css" />
    <link rel="icon" type="image/png" href="./favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="./favicon-16x16.png" sizes="16x16" />
  </head>

  <body>
    <?php if (isset($origin) and isset($timeseries_id)) { ?>
      <div id="timeseries_id" style="background-color:#f0e79a; font-family:sans-serif; padding:1em; font-size:2em"><i>From <a href='<?php echo $origin; ?>'><?php echo $origin; ?></a></i>: The <u>timeseries id</u> to use is: <b><?php echo $timeseries_id ?></b></div>
    <?php } ?>
    <div id="swagger-ui"></div>
    <script src="./swagger-ui-bundle.js" charset="UTF-8"> </script>
    <script src="./swagger-ui-standalone-preset.js" charset="UTF-8"> </script>
    <script>
        window.onload = function() {
            // the following lines will be replaced by docker/configurator, when it runs in a docker-container
            window.ui = SwaggerUIBundle({
                url: "/tsdws/swagger/oedatarep-tsdsystem.json",
                dom_id: '#swagger-ui',
                deepLinking: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                ],
                layout: "StandaloneLayout" <?php if ($token) { ?>,
                onComplete: function() {
                    ui.preauthorizeApiKey("Bearer", "<?php echo $token; ?>");
                }
                <?php } ?>
            });
        };
    </script>
  </body>
</html>

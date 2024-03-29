<?php
    include "manage_requests.php";
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <script src="../js-download/axios.min.js"></script>
    <script src="../js-download/jquery-3.6.0.min.js"></script>

    <!-- Load required Bootstrap and BootstrapVue CSS -->
    <link type="text/css" rel="stylesheet" href="../js-download/bootstrap.min.css" />
    <link type="text/css" rel="stylesheet" href="../js-download/bootstrap-vue.min.css" />

     <!-- Load Vue followed by BootstrapVue -->
    <script src="../js-download/vue.min.js"></script>
    <script src="../js-download/bootstrap-vue.min.js"></script>

    <!-- Load the following for BootstrapVueIcons support -->
    <script src="../js-download/bootstrap-vue-icons.min.js"></script>

    <!-- Latest compiled and minified plotly.js JavaScript -->
    <script src="../js-download/plotly-latest.min.js"></script>
    <!--<script src="https://cdn.plot.ly/plotly-2.18.2.min.js"></script>-->

    <script>
        var charts = JSON.parse('<?php echo json_encode($charts); ?>');
        //console.log(charts);
    </script>
    <!-- Load custom script -->
    <script src="index.js"></script>
    <script src="vue-reactive-chart.js"></script>

    <!-- Load custom css -->
    <link rel="stylesheet" type="text/css" href="index.css">
</head>

<body>
    <div id="app" class="container">
        <?php 
        foreach($charts as $i => $chart) {
            if (count($chart["traces"]) > 0) {
                echo "<plotly-chart :chart='charts[".strval($i)."]'></plotly-chart>";
            } else {
                echo '
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <b>Error</b>: Empty columns list for request (name = "<b>' . $chart["layout"]["title"] . '"</b> - id = "' . $chart["uuid"] . '")
                </div>
                ';
            }
        }
        ?>
    </div>
</body>

</html>
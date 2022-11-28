<?php
    $requests = json_decode($_POST["requests"], TRUE);
    //var_dump($requests);

    // charts to pass to Vue component - build from $requests var
    $charts = array();

    // loop each request
    foreach($requests as $index => $request) {
        
        // define a new chart
        $chart = array();

        // set chart properties
        $chart["uuid"] = $request["request_id"];
        $chart["traces"] = array();

        // add traces
        foreach($request["columns"] as $key => $value) {
            $r = $request;
            $r["columns"] = array($value);
            $trace = array(
                "request" => $r,
                "x" => [],
                "y" => [],
                "name" => $r["columns"][0],
                "mode" => "markers",
                "type" => "scatter",
                "line" => array(
                    "color" => "#000000",
                    "width" => 1,
                    "shape" => "line"
                ),
                "marker" => array(
                    "color" => "#000000",
                    "size" => 8
                ),
            );
            if ($key > 0) {
                $trace["yaxis"] = "y".strval($key+1);
            }
            array_push($chart["traces"], $trace);
        }

        // add layout
        $chart["layout"] = array(
            "title" => $request["title"],
            "xaxis" => array(
                "title" => "time"
            ),
            "legend" => array(
                "showLegend" => true,
                "orientation" => "h",
                "x" => 0,
                "y" => -0.3
            )
        );
        if (count($request["columns"]) > 0) {
            $diff = count($request["columns"])%2==0 ? [3, 3] : [2, 3];
            $xaxis_domain = [
                (0.1 * (count($request["columns"])-$diff[0]) + 0.005), 
                1 - (0.1 * (count($request["columns"])-$diff[1]) + 0.005)
            ];
            $chart["layout"]["xaxis"]["domain"] = $xaxis_domain;
        }

        // prepare yaxes for layout
        foreach($request["columns"] as $key => $value) {
            $label = "yaxis" . ($key == 0 ? "" : strval($key+1));
            $chart["layout"][$label] = array(
                "title" => $request["columns"][$key],
            );    
            $step = 0.1 * floor($key/2);
            $position = ($key%2==0) ? $step : (1-$step);
            $chart["layout"][$label]["position"] = $position;
            if ($key > 0) {
                $chart["layout"][$label]["anchor"] = "free";
                $chart["layout"][$label]["overlaying"] = "y";
                $chart["layout"][$label]["side"] = $key%2==0 ? "left": "right";
                $chart["layout"][$label]["type"] = "linear";
            }
        }

        // push to charts list
        array_push($charts, $chart);
    }

    //echo "<div style='font-size:8px'>".json_encode($charts)."</div>";
?>
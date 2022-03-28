<?php
    $body = file_get_contents('php://input');
    //echo $body;
    $request = json_decode($body, TRUE);
    //var_dump($request);

    session_start();
    $userId = isset($_SESSION["userId"]) ? $_SESSION["userId"] : 1;

    // load users class
    require_once("..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR."Tokens.php");
    $myObj = new Tokens(array("scope"=>"timeseries-read"));
    
    $token = $myObj->generateOnTheFly($userId);
    //echo $token;
    
    $dataArray = ['request' => $body];
    $data = http_build_query($dataArray);
    $url = "http://localhost/tsdws/timeseries/values/?" . $data;
    ///echo $url;
    
    // prepare cURL request
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, true);  // RETURN HTTP HEADERS
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        "Authorization: $token", // set Authorization header with the token generated for the session user
    ));
    
    // call request
    $resp = curl_exec($curl);
    curl_close($curl);

    // read header from called url to transfer to this request
    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    $header = substr($resp, 0, $header_size);
    $body = substr($resp, $header_size, strlen($resp));

    // set this request header the same of the returned by the cURL request
    $headerList = explode(PHP_EOL, $header);
    foreach($headerList as $h) {
        header($h);
    }
    
    // echo body response
    echo $body;
?>
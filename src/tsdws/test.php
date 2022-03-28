<?php
$dsn = "pgsql:host=db;dbname=tsdsystem;port=5432";
$conn = new PDO($dsn, "webapp_user", "smoketest");
$sqlQuery = "SELECT * FROM tsd_main.timeseries";
$sqlResult = $conn->query($sqlQuery);
$rows = $sqlResult->fetchAll(PDO::FETCH_ASSOC);
$conn = null;
var_dump($rows);

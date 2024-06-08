<?php

// Database Configuration

$dbHost = "localhost";
$dbUser = "cooldemo3";
$dbPW = "123456";
$dbName = "cooldemo3";

// Establish connection

$mysqli = mysqli_connect($dbHost, $dbUser, $dbPW, $dbName);

// Check connection
if(mysqli_connect_errno()){
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

// Website

$Website_Url = "https://coolscript.hu/demo3/";

?>

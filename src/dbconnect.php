<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $host = "localhost";
    $user = "root";
    $pass = "";
    $db = "anyquiz1202_v2.0";

    //create connection
    $conn = mysqli_connect($host,$user,$pass,$db);
    
    if(!$conn){
        die("Connection failed: ".mysqli_connect_error());
    }

?>
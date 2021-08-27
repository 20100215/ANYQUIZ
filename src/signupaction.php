<?php

    include_once("dbconnect.php");
    $isValid = true;
    $status = 400;
    $retVal = "";
    $data = [];

    //Getting user data
    $username = trim($_REQUEST['username']);
    $email = trim($_REQUEST['email']);
    $password = $_REQUEST['password'];
    $confirmpassword = $_REQUEST['confirmpassword'];

    //Check if there are missing fields
    if($username=="" || $email=="" || $password=="" || $confirmpassword==""){
        $isValid = false;
        $retVal = "<b>Error: Please input all fields.</b> ";
    }

    //Check if email is valid or not
    if($isValid && !filter_var($email,FILTER_VALIDATE_EMAIL)){
        $isValid = false;
        $retVal = "<b>Error: Invalid email address entered.</b> ";
    }

    //Check if passwords match
    if($isValid && $password != $confirmpassword){
        $isValid = false;
        $retVal = "<b>Error: Confirm password is not the same as the password entered.</b> ";
    }

    //Check if agreed to terms and conditions
    if($isValid && !isset($_REQUEST['agree'])){
        $isValid = false;
        $retVal = "<b>Please agree to the terms and conditions to proceed.</b> ";
    }

    //Check if username already exists
    if($isValid){
        $sql = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $sql->bind_param("s",$username);
        $sql->execute();
        $result = $sql->get_result();
        $sql->close();
        if($result->num_rows > 0){
            $isValid = false;
            $retVal = "<b>Error: Username already exists.</b> ";
        }
    }
    
    //Insert new user account to database
    if($isValid){
        $sql = $conn->prepare("INSERT INTO users(`username`,`email`,`password`,`last_active`) VALUES (?,?,?,ADDTIME(CURRENT_TIMESTAMP,'7:0:0'))");
        $password = password_hash($password,PASSWORD_DEFAULT);
        $sql->bind_param("sss",$username,$email,$password);
        $sql->execute();
        $sql->close();
        $retVal = "<b>Account created successfully. Please proceed to the login page and enter your newly typed credentials.</b>";
        unset($_SESSION['username']);
        $status = 200;
    }

    //store to JSON object and forward to JS
    $retObj = array(
        'status' => $status,
        'message' => $retVal
    );
    $myJSON = json_encode($retObj, JSON_FORCE_OBJECT);
    echo $myJSON;

?>
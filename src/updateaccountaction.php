<?php

    include_once("dbconnect.php");
    $isValid = true;
    $status = 400;
    $retVal = "";
    $data = [];

    //Getting user data
    $username = trim($_REQUEST['username']);
    $email = trim($_REQUEST['email']);
    $oldpassword = $_REQUEST['oldpassword'];
    $password = $_REQUEST['password'];
    $confirmpassword = $_REQUEST['confirmpassword'];
    $userid = trim($_REQUEST['userid']);

    //Check if there are missing fields
    if($username=="" || $email=="" || $oldpassword==""){
        $isValid = false;
        $retVal = "<b>Error: </b> Please input all fields.";
    }

    if($isValid){
        $sql = $conn->prepare("SELECT * FROM users WHERE `user_id` = ?");
        $sql->bind_param("s",$userid);
        $sql->execute();
        $result = $sql->get_result();
        $obj = mysqli_fetch_object($result);
        $sql->close();
        if($result->num_rows > 0){
            $isCorrectPassword = password_verify($oldpassword,$obj->password);
            if(!$isCorrectPassword){
                $isValid = false;
                $retVal = "<b>Error: </b> Incorrect password entered.";
            }
        }
    }

    if($isValid && isset($_REQUEST['changepassword']) && ($password=="" || $confirmpassword=="")){
        $isValid = false;
        $retVal = "<b>Error: </b> Please input all fields.";
    }

    //Check if email is valid or not
    if($isValid && !filter_var($email,FILTER_VALIDATE_EMAIL)){
        $isValid = false;
        $retVal = "<b>Error: </b> Invalid email address entered.";
    }

    //Check if passwords match
    if($isValid && isset($_REQUEST['changepassword']) && $password != $confirmpassword){
        $isValid = false;
        $retVal = "<b>Error: </b> Confirm password is not the same as the password entered.";
    }

    //Check if username already exists
    if($isValid){
        $sql = $conn->prepare("SELECT * FROM users WHERE `username` = ? AND `user_id` <> ? ");
        $sql->bind_param("ss",$username,$userid);
        $sql->execute();
        $result = $sql->get_result();
        $sql->close();
        if($result->num_rows > 0){
            $isValid = false;
            $retVal = "<b>Error: </b> Error: Username already exists.";
        }
    }
    
    $pass = (isset($_REQUEST['changepassword'])) ? $password : $oldpassword;

    //Insert new user account to database
    if($isValid){
        $sql = $conn->prepare("UPDATE `users` SET `username`= ?,`email`= ?,`password`= ? WHERE `user_id` = ?");
        $pass = password_hash($pass,PASSWORD_DEFAULT);
        $sql->bind_param("ssss",$username,$email,$pass,$userid);
        $sql->execute();
        $sql->close();
        $_SESSION['username'] = $username;
        $retVal = "<b>Success: </b> Account information updated successfully.";
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
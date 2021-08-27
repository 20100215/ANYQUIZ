<?php

    include_once("dbconnect.php");
    $isValid = true;
    $status = 400;
    $retVal = "";
    $data = [];

    //Getting user data
    $name = trim($_REQUEST['name']);
    $email = trim($_REQUEST['email']);
    $subject = trim($_REQUEST['subject']);
    $message = trim($_REQUEST['message']);

    //Check if there are missing fields
    if($name=="" || $email=="" || $subject=="" || $message==""){
        $isValid = false;
        $retVal = "Please input all fields.";
    }

    //Check if email is valid or not
    if($isValid && !filter_var($email,FILTER_VALIDATE_EMAIL)){
        $isValid = false;
        $retVal = "Invalid email address entered.";
    }

    //Insert new contact form entry to database
    if($isValid){
        $sql = $conn->prepare("INSERT INTO `feedbacks`(`name`, `email`, `subject`, `message`,`date`) VALUES (?,?,?,?,ADDTIME(CURRENT_TIMESTAMP(),'7:0:0'))");
        $sql->bind_param("ssss",$name,$email,$subject,$message);
        $sql->execute();
        $sql->close();
        $retVal = "Your message has been sent. Thank you!";
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
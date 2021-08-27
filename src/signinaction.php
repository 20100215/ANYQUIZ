<?php

$isValid = true;
$status = 400;
$retVal = "<b>An internal error occurred (unable to connect to database)</b>";
$data = [];
include_once("../src/dbconnect.php");

//Getting user data
$username = trim($_REQUEST['loginUser']);
$password = $_REQUEST['loginPassword'];

//Check if there are missing fields
if ($username == "" || $password == "") {
    $isValid = false;
    $retVal = "<b>Error: Please input all fields.</b>";
}

//Check if username exists
if ($isValid) {
    $sql = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $sql->bind_param("s", $username);
    $sql->execute();
    $result = $sql->get_result();
    $obj = mysqli_fetch_object($result);
    $sql->close();
    if ($result->num_rows > 0) {
        //Username retrieved, proceed to check password
        $isCorrectPassword = password_verify($password, $obj->password);
        if ($isCorrectPassword == true) {
            $retVal = "Success";
            $status = 200;
            $data = $obj;
            //Set session to contain user details
            $_SESSION['userid'] = $obj->user_id;
            $_SESSION['username'] = $obj->username;
            $_SESSION['status'] = $obj->user_status;
            $_SESSION['showwelcomeinfo'] = 0;
            //update last active time
            $sql = $conn->prepare("UPDATE users SET `last_active` = ADDTIME(CURRENT_TIMESTAMP,'7:0:0') WHERE `user_id` = ?");
            $sql->bind_param("s", $_SESSION['userid']);
            $sql->execute();
            $sql->close();
        } else {
            $retVal = "<b>Error: Invalid username or password</b> ";
        }
    } else {
        $retVal = "<b>Error: Account does not exist</b> ";
    }
}

//store to JSON object and forward to JS
$retObj = array(
    'status' => $status,
    'data' => $data,
    'message' => $retVal
);
$myJSON = json_encode($retObj, JSON_FORCE_OBJECT);
echo $myJSON;

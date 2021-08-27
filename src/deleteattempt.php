<?php

    include_once("dbconnect.php");
    $isValid = true;
    $status = 400;
    $retVal = "";
    $data = [];

    $quiztitle = $_GET['quiztitle'];
    $quizid = $_GET['quizid'];
    $quizcode = $_GET['quizcode'];
    $resultid = $_REQUEST['resultid'];

    //delete attempt
        $sql = $conn->prepare("DELETE FROM `results` WHERE result_id = ?");
        $sql->bind_param("s",$resultid);
        if ($sql->execute()){
            $status = 200;
            $retVal = "<b>Attempt record successfully removed!</b>";
        } else {
            $retVal = "<b>Error in removing attempt record. Please try again.</b>";
        }
        $sql->close();

    //store to JSON object and forward to JS
    $retObj = array(
        'status' => $status,
        'message' => $retVal
    );
    $myJSON = json_encode($retObj, JSON_FORCE_OBJECT);
    echo $myJSON;
?>
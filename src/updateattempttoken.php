<?php

    include_once("dbconnect.php");    
    $isValid = true;
    $status = 400;
    $retVal = "";
    $data = [];

    //Insert quiz result
        $sql = $conn->prepare("UPDATE `results` SET `score`= ? , `perfect_score` = ?,`duration`= ?, `is_final` = ? WHERE `result_id`= ?");
        $sql->bind_param("sssss",$_REQUEST['totalscore'],$_REQUEST['perfectscore'],$_REQUEST['attempttime'],$_REQUEST['isfinal'],$_REQUEST['attemptid']);
        if ($sql->execute()){
            $status = 200;
            $retVal = "<b>Quiz successfully submitted!</b>";
        } else {
            $retVal = "<b>Error in submitting quiz. Please try submitting again.</b>";
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
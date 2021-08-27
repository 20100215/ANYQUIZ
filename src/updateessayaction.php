<?php

include_once("dbconnect.php");    
$isValid = true;
$status = 400;
$retVal = "";
$data = [];

$numessays = $_REQUEST['numessays'];
$resultid = $_REQUEST['resultid'];
$partialpoints = $_REQUEST['partialpoints'];
$isfinal = 1;
$pointstoadd = 0;

//update essay responses
    for($count = 1; $count <= $numessays; $count++){
        //set data and update only when points is assigned and set
        if (!isset($_REQUEST['points'.$count])){
            $isfinal = 0;
            $points = -1;
        } else if ($_REQUEST['points'.$count] == ""){
            $isfinal = 0;
            $points = -1;
        } else {
            $points = $_REQUEST['points'.$count];
            $pointstoadd += $points;    
        }

        $essayid = $_REQUEST['essayid'.$count];
        $feedback = isset($_REQUEST['feedback'.$count]) ? $_REQUEST['feedback'.$count] : NULL ;
        
        $sql = $conn->prepare("UPDATE `essay_responses` SET `points`= ? , `feedback` = ? WHERE `essay_id`= ?");
        $sql->bind_param("sss",$points,$feedback,$essayid);
        $sql->execute();
        $sql->close();
    }   

//update final score
    $sql = $conn->prepare("UPDATE `results` SET `score`= `score` - ? + ?, `is_final` = ? WHERE `result_id`= ?");
    $sql->bind_param("ssss",$partialpoints,$pointstoadd,$isfinal,$resultid);
    $sql->execute();
    $sql->close();

    $diff = 0 - $partialpoints + $pointstoadd;

    //store to JSON object and forward to JS
    $retObj = array(
        'status' => 200,
        'diff' => $diff,
        'newpartialpoints' => $pointstoadd
    );
    $myJSON = json_encode($retObj, JSON_FORCE_OBJECT);
    echo $myJSON;

?>
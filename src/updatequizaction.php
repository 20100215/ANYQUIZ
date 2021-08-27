<?php

$status = 400;
$retVal = "<b>An internal error occurred (unable to connect to database)</b>";
$data = [];
include_once("dbconnect.php");
$quizid = $_REQUEST['quizid'];
$userid = $_REQUEST['userid'];


    $quiztitle = trim($_REQUEST['quizname']);
    $quizdesc = trim($_REQUEST['quizdescription']);
    $timelimit = trim($_REQUEST['istimelimit'] == "1") ? trim($_REQUEST['timelimit']) : -1 ;
    $attemptlimit = trim($_REQUEST['isattemptlimit'] == "1") ? trim($_REQUEST['attemptlimit']) : -1;
    $isbacktrack = trim($_REQUEST['isbacktrack']);
    $isshufflequestionorder = trim($_REQUEST['isshufflequestionorder']);
    $isexammode = trim($_REQUEST['isexammode']);
    $accessibility = trim($_REQUEST['accessibility']);
    $isviewquestions = trim($_REQUEST['isviewquestions']);
    if ($isviewquestions == 1){
        $isshowcorrectanswers = trim($_REQUEST['isshowcorrectanswers']);
    } else {
        $isshowcorrectanswers = 0;
    }

    $sql = $conn->prepare("UPDATE `quizzes` SET `quiz_title`= ? ,`quiz_desc`= ? ,`time_limit`= ? ,`attempt_limit`= ? ,`is_allowbacktrack`= ? ,`is_shufflequestionorder`= ?,`is_exammode`= ?,`is_public`= ?,`is_viewquestions`= ? ,`is_showcorrectanswers`= ? , `last_active` = ADDTIME(CURRENT_TIMESTAMP,'7:0:0') WHERE quiz_id = ? AND from_user_id = ?");
    $sql->bind_param("ssssssssssss",$quiztitle,$quizdesc,$timelimit,$attemptlimit,$isbacktrack,$isshufflequestionorder,$isexammode,$accessibility,$isviewquestions,$isshowcorrectanswers,$quizid,$userid);
    if($sql->execute()){
        $status = 200;
        $retVal = "Success";
    }
    $sql->close();

//store to JSON object and forward to JS
$retObj = array(
    'status' => $status,
    'data' => $data,
    'message' => $retVal
);
$myJSON = json_encode($retObj, JSON_FORCE_OBJECT);
echo $myJSON;

?>
<?php

    include_once("dbconnect.php");
    /* Default values - already set in database!*/

    //Insert quiz result
        $sql = $conn->prepare("INSERT INTO `results` (`from_quiz_id`, `from_user_id`,`attempt_datetime`) VALUES (?,?,ADDTIME(CURRENT_TIMESTAMP,'7:0:0'))");
        $sql->bind_param("ss",$_REQUEST['quizid'],$_SESSION['userid']);
        $sql->execute();
        $attemptid = $sql->insert_id;
        $sql->close();

    //store to JSON object and forward to JS
    $retObj = array(
        'attemptid' => $attemptid
    );
    $myJSON = json_encode($retObj, JSON_FORCE_OBJECT);
    echo $myJSON;
?>
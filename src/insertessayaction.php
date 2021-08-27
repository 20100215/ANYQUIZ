<?php
    include_once("dbconnect.php");
    /* Default values - already set in database!*/

    //Insert essay response
        $sql = $conn->prepare("INSERT INTO `essay_responses` (`from_result_id`, `from_question_id`, `question_no_label`, `answer`, `points`) VALUES (?,?,?,?,?)");
        $sql->bind_param("sssss",$_REQUEST['resultid'],$_REQUEST['questionid'],$_REQUEST['questionnum'],$_REQUEST['answer'],$_REQUEST['points']);
        $sql->execute();
        $sql->close();

    //store to JSON object and forward to JS
    $retObj = array(
        'status' => 200,
        'message' => "ok"
    );
    $myJSON = json_encode($retObj, JSON_FORCE_OBJECT);
    echo $myJSON;
?>
<?php

    include_once("dbconnect.php");

    //delete quiz
        $sql = $conn->prepare("DELETE FROM `quizzes` WHERE quiz_id = ?");
        $sql->bind_param("s",$_REQUEST['quizid']);
        $sql->execute();
        $sql->close();

    //redirect to quizzes page
    header("url: ../quiz.php");
    exit();
?>
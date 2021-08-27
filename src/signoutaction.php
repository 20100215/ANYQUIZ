<?php
    session_start();
    session_destroy();
    $retObj = array(
        'status' => 200,
        'message' => "Success"
    );
    $myJSON = json_encode($retObj,JSON_FORCE_OBJECT);
    echo $myJSON;
?>
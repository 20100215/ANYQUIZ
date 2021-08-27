<?php
    $status = 200;
    $retVal = "Success";

    //this is only for chccking connection. If this works, return the JSON,
    //if client is able to receive and parse JSON, then proceed to perform
    //actual client-server action or redirect to specified page

    //store to JSON object and forward to JS
    $retObj = array(
        'status' => $status,
        'message' => $retVal
    );
    $myJSON = json_encode($retObj, JSON_FORCE_OBJECT);
    echo $myJSON;
?>
<?php
include_once("../src/dbconnect.php");
$status = 200;
$data = "error";
/* get quiz code */
$quizcode = $_REQUEST['quizcode'];

/* check if quiz code exists or not */
$sql = $conn->prepare("SELECT * FROM quizzes WHERE access_code = ?");
$sql->bind_param("s", $quizcode);
$sql->execute();
$result = $sql->get_result();
if ($result->num_rows == 0) {
    $data = "ok";
} else {
    $data = "duplicated";
}
$sql->close();


//store to JSON object and forward to JS
$retObj = array(
    'status' => $status,
    'data' => $data,
);
$myJSON = json_encode($retObj, JSON_FORCE_OBJECT);
echo $myJSON;

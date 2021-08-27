<?php

$status = 400;
$retVal = "<b>An internal error occurred (unable to connect to database)</b>";
$data = [];
include_once("dbconnect.php");

/* inserting the quiz details */
$isbacktrack = trim($_REQUEST['isbacktrack']);
$isshufflequestionorder = trim($_REQUEST['isshufflequestionorder']) == "Yes" ? 1 : 0;
$isexammode = trim($_REQUEST['isexammode']);
$ispublic = trim($_REQUEST['accessibility']) == "Enabled" ? 1 : 0;
$isviewquestions = trim($_REQUEST['isviewquestions']) == "Enabled" ? 1 : 0;

if ($_REQUEST['hasessay'] == "1" && $_REQUEST['showessays'] == "1") {
  $isshufflequestionorder = 2;
}

if (trim($_REQUEST['isviewquestions']) == "Enabled") {
  $isshowcorrectanswers = trim($_REQUEST['isshowcorrectanswers']) == "Enabled" ? 1 : 0;
} else {
  $isshowcorrectanswers = 0;
}

$time = trim($_REQUEST['istimelimit']) == "Enabled" ? trim($_REQUEST['timelimit']) : -1;
$attempt = trim($_REQUEST['isattemptlimit']) == "Enabled" ? trim($_REQUEST['attemptlimit']) : -1;
$hasessay = trim($_REQUEST['hasessay']);

$sql = $conn->prepare("INSERT INTO `quizzes`(`from_user_id`, `quiz_title`, `quiz_desc`, `time_limit`, `attempt_limit`, `is_allowbacktrack`, `is_shufflequestionorder`, `is_exammode`, `is_public`, `is_viewquestions`, `is_showcorrectanswers`, `has_essay`, `access_code`,`last_active`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,ADDTIME(CURRENT_TIMESTAMP,'7:0:0'))");
$sql->bind_param("sssssssssssss", $_REQUEST['userid'], $_REQUEST['quizname'], $_REQUEST['quizdescription'], $time, $attempt, $isbacktrack, $isshufflequestionorder, $isexammode, $ispublic, $isviewquestions, $isshowcorrectanswers, $hasessay, $_REQUEST['quizcode']);
if ($sql->execute()) {
  //no problem in data insertion to database
  $status = 200;
  $retVal = "Success";
  $_SESSION['insertquizstatus'] = 1;
}
/* get quiz id */
$quizid = $sql->insert_id;
$sql->close();

/* inserting the question details */

/* count questions and points*/
$count = count($_REQUEST['questiontype']);

/* insert questions (only when quiz insertion is successful)*/
if ($status == 200) {
  for ($x = 0; $x < $count; $x++) {
    $sql = $conn->prepare("INSERT INTO questions (`from_quiz_id`, `question_text`, `question_type`, `points`) VALUES (?,?,?,?)");
    $sql->bind_param("ssss", $quizid, ($_REQUEST['questiondescription'][$x]), ($_REQUEST['questiontype'][$x]), ($_REQUEST['points'][$x]));
    $sql->execute();
    /* get question id */
    $questionid = $sql->insert_id;
    $sql->close();

    if (trim($_REQUEST['questiontype'][$x]) == "TF") {
      $sql = $conn->prepare("INSERT INTO `type_t/f`(`from_question_id`, `answer`) VALUES (?,?)");
      $sql->bind_param("ss", $questionid, ($_REQUEST['TFcorr' . $x]));
      $sql->execute();
      $sql->close();
    }

    if (trim($_REQUEST['questiontype'][$x]) == "MTF") {
      for($ctr = 1; $ctr <= $_REQUEST['numstatements' . $x]; $ctr++){
        $sql = $conn->prepare("INSERT INTO `type_mtf`(`from_question_id`, `statement`, `state`) VALUES (?,?,?)");
        $sql->bind_param("sss", $questionid, ($_REQUEST['MTF' . $x . '-' . $ctr]), ($_REQUEST['MTFcorr' . $x . '-' . $ctr]));
        $sql->execute();
      }
    }

    if (trim($_REQUEST['questiontype'][$x]) == "MCQ") {
      $sql = $conn->prepare("INSERT INTO `type_mcq`(`from_question_id`, `option1`, `option2`, `option3`, `option4`, `option5`, `correct_option_num`) VALUES (?,?,?,?,?,?,?)");
      $sql->bind_param("sssssss", $questionid, ($_REQUEST['MCQ1'][$x]), ($_REQUEST['MCQ2'][$x]), ($_REQUEST['MCQ3'][$x]), ($_REQUEST['MCQ4'][$x]), ($_REQUEST['MCQ5'][$x]), ($_REQUEST['MCQcorr' . $x]));
      $sql->execute();
      $sql->close();
    }

    if (trim($_REQUEST['questiontype'][$x]) == "MCMAQ") {
      $optioncount = 1;
      foreach ($_REQUEST['MCMAQ' . $x] as $y) {
        if ($y != "") {
          //get is_correct value
          if (isset($_REQUEST['MCMAQcorr' . $x . '-' . $optioncount])) {
            $iscorrect = 1;
          } else {
            $iscorrect = 0;
          }
          //insert to database
          $sql = $conn->prepare("INSERT INTO `type_mcmaq`(`from_question_id`, `optionlabel`, `is_correct`) VALUES (?,?,?)");
          $sql->bind_param("sss", $questionid, $y, $iscorrect);
          $sql->execute();
          $sql->close();
        }
        $optioncount++;
      }
    }

    if (trim($_REQUEST['questiontype'][$x]) == "FITBQ") {
      foreach ($_REQUEST['FITB' . $x] as $y) {
        if ($y != "") {
          $sql = $conn->prepare("INSERT INTO `type_fitbq`(`from_question_id`, `accepted_answer`) VALUES (?,?)");
          $sql->bind_param("ss", $questionid, $y);
          $sql->execute();
          $sql->close();
        }
      }
    }

    /* Nothing to do for essay */
  }
}

//clear $_REQUEST content
unset($_REQUEST['quizname']);

//store to JSON object and forward to JS
$retObj = array(
  'status' => $status,
  'message' => $retVal
);
$myJSON = json_encode($retObj, JSON_FORCE_OBJECT);
echo $myJSON;

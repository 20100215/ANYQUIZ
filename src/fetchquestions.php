<?php

$status = 200;
$retVal = "<b>An internal error occurred (unable to connect to database)</b>";
$data = "";
include_once("../src/dbconnect.php");

if (!isset($_REQUEST["quizid"]) || !isset($_REQUEST["isshufflequestionorder"]) || !isset($_REQUEST["numquestions"]) || !isset($_REQUEST["isbacktrack"])) {
    //incomplete data, exit
    $status = 400;
}

if ($status == 200) {
    $quizid = $_REQUEST["quizid"];
    $isshufflequestionorder = $_REQUEST["isshufflequestionorder"];
    $numquestions = $_REQUEST["numquestions"];
    $isbacktrack = $_REQUEST["isbacktrack"];

    //update last active time
    $sql = $conn->prepare("UPDATE quizzes SET `last_active` = ADDTIME(CURRENT_TIMESTAMP,'7:0:0') WHERE `quiz_id` = ?");
    $sql->bind_param("s", $_REQUEST['quizid']);
    $sql->execute();
    $sql->close();

    /* prepare questions  (`from_quiz_id`, `question_text`, `question_type`, `points`)*/
    /* if isshufflequestionorder == 2 -> prepare objective type questions first before essay */
    if ($isshufflequestionorder == 2) {
        $sql = $conn->prepare("SELECT * FROM questions WHERE from_quiz_id = ? AND question_type <> 'ESSAY'");
    } else {
        $sql = $conn->prepare("SELECT * FROM questions WHERE from_quiz_id = ?");
    }
    $sql->bind_param("s", $quizid);
    $sql->execute();
    $questionlist = $sql->get_result();
    $numitems = $questionlist->num_rows;
    $sql->close();

    /* store questionlist to array */
    $questionarray = [];
    while ($row = $questionlist->fetch_assoc()) {
        array_push($questionarray, $row);
    }

    /* check if need to shuffle questions */
    if ($isshufflequestionorder >= 1) {
        shuffle($questionarray);
    }

    /* display questions */
    for ($count = 1; $count <= $numitems; $count++) {
        $questionid = $questionarray[$count - 1]['question_id'];
        $questiontext = nl2br($questionarray[$count - 1]['question_text']);
        $questionpoints = $questionarray[$count - 1]['points'];
        $questiontype = $questionarray[$count - 1]['question_type'];

        $data .= <<<EOT
                                <div class="question">
                                <div class="row">
                                    <div class="count questionnum" style="padding-left: 35px;">
                                        Question <span id="question$count">$count</span> of $numquestions<span style="font-size:17px;">&nbsp; &nbsp;
                                        (<span class="obtainedpoints" style="display:none;"></span><span class="points">$questionpoints</span> points)</span>
                                        <span style="display:none;" class="questiontype">$questiontype</span>
                                        <span style="display:none;" class="questionid">$questionid</span>
                                    </div>
                                </div>
                                <div class="count-7" style="padding-left: 60px; font-size: 20px; padding-right:20%">
                                    $questiontext
                            EOT;

        /* True or false (single statement) */
        if ($questionarray[$count - 1]['question_type'] == "TF") {
            $data .= <<<EOT
                            <div class="TF" style="padding-left: 15px; font-size: 18px; padding-top: 20px;">
                        EOT;
            $data .= ('<input type="radio" id="TF' . $count . '-TRUE" name="TF' . $count . '" value="TRUE"><label for="TF' . $count . '-TRUE" style="min-width:150px;">&nbsp;&nbsp;TRUE</label><br>');
            $data .= ('<input type="radio" id="TF' . $count . '-FALSE" name="TF' . $count . '" value="FALSE"><label for="TF' . $count . '-FALSE" style="min-width:150px;">&nbsp;&nbsp;FALSE</label><br>');

            //prepare answer
            $sql = $conn->prepare("SELECT * FROM `type_t/f` WHERE from_question_id = ?");
            $sql->bind_param("s", $questionarray[$count - 1]['question_id']);
            $sql->execute();
            $result = $sql->get_result();
            $sql->close();
            while ($row2 = $result->fetch_assoc()) {
                $corr = ($row2['answer']);
            }
            $data .= <<<EOT
                            </div>
                            <span id='ans$count' style='display:none;'>$corr
                        EOT;
        }

        /* True or false (multiple statements) */
        if ($questionarray[$count - 1]['question_type'] == "MTF") {
            $data .= <<<EOT
                            <div class="MTF" style="padding-left: 15px; font-size: 18px; padding-top: 20px;">
                        EOT;

            // prepare statements
            $sql = $conn->prepare("SELECT * FROM `type_mtf` WHERE from_question_id = ?");
            $sql->bind_param("s", $questionarray[$count - 1]['question_id']);
            $sql->execute();
            $result = $sql->get_result();
            $numstatements = $result->num_rows;
            $sql->close();

            $statementarray = [];
            while ($row2 = $result->fetch_assoc()) {
                array_push($statementarray, $row2);
            }

            // shuffle array of statements 
            shuffle($statementarray);
            $pos = array('A','B','C');
            $corr = 0;
            $mult = 1;

            // output statements and store the states
            for($ctr = 1; $ctr <= $numstatements; $ctr++){               
                $data .= ($pos[$ctr-1]. ".&nbsp; " . $statementarray[$ctr-1]['statement'] . "<br>");
                $corr += ($statementarray[$ctr-1]['state'] == "TRUE") ? $mult : 0;
                $mult *= 2;
            }

            // prepare the dropdown with values
            $data .= "<br>Select option: &nbsp; &nbsp; <select id='MTF$count' style='font-size: 15px;'>";

            if($numstatements == 2){
                $data .= <<<EOT
                            <option value='-1'>(Select)</option>
                            <option value='1'>Only statement A is true.</option>
                            <option value='2'>Only statement B is true.</option>
                            <option value='3'>Both statements are true.</option>
                            <option value='0'>Neither of the statements is true.</option>
                        EOT;
            } else { /*numstatements == 3*/
                $data .= <<<EOT
                            <option value='-1'>(Select)</option>
                            <option value='1'>Only statement A is true.</option>
                            <option value='2'>Only statement B is true.</option>
                            <option value='4'>Only statement C is true.</option>
                            <option value='3'>Only statements A and B are true.</option>
                            <option value='5'>Only statements A and C are true.</option>
                            <option value='6'>Only statements B and C are true.</option>
                            <option value='7'>All statements are true.</option>
                            <option value='0'>Neither of the statements is true.</option>
                        EOT;
            }

            $data .= <<<EOT
                            </select>
                            </div>
                            <span id='ans$count' style='display:none;'>$corr
                        EOT;
        }

        /* Multiple choice - single answer */
        if ($questionarray[$count - 1]['question_type'] == "MCQ") {

            $data .= <<<EOT
                            <div class="MCQ" style="padding-left: 15px; font-size: 18px; padding-top: 20px;">
                        EOT;

            $sql = $conn->prepare("SELECT * FROM `type_mcq` WHERE from_question_id = ?");
            $sql->bind_param("s", $questionarray[$count - 1]['question_id']);
            $sql->execute();
            $result = $sql->get_result();
            $sql->close();

            // shuffle choices order
            $optionarray = [0, 1, 2, 3, 4];
            while ($row2 = $result->fetch_assoc()) {
                $optionlabelarray = [$row2["option1"], $row2["option2"], $row2["option3"], $row2["option4"], $row2["option5"]];
                $corr = $row2["correct_option_num"];
            }

            shuffle($optionarray);

            for ($n = 0; $n < 5; $n++) {
                if ($optionlabelarray[$optionarray[$n]] != "")
                    $data .= ('<input type="radio" id="MCQ' . $count . '-' . ($optionarray[$n] + 1) . '" name="MCQ' . $count . '" value="' . ($optionarray[$n] + 1) . '" ><label for="MCQ' . $count . '-' . ($optionarray[$n] + 1) . '" style="min-width:150px;">&nbsp;&nbsp;' . $optionlabelarray[$optionarray[$n]] . '</label> <br>');
            }

            $data .= <<<EOT
                            </div>
                            <span id='ans$count' style='display:none;'>$corr
                        EOT;
        }

        /* Multiple choice - multiple answers */
        if ($questionarray[$count - 1]['question_type'] == "MCMAQ") {

            $data .= <<<EOT
                            <div class="MCMAQ" style="padding-left: 15px; font-size: 18px; padding-top: 20px;">
                        EOT;

            $optionarray = [];
            $optionlabelarray = [];
            $corrarray = [];
            $corranswers = [];
            $optioncount = 0;

            //prepare option labels
            $sql = $conn->prepare("SELECT * FROM `type_mcmaq` WHERE from_question_id = ?");
            $sql->bind_param("s", $questionarray[$count - 1]['question_id']);
            $sql->execute();
            $result = $sql->get_result();
            $sql->close();

            //insert option labels and answers to array
            $optioncount = 0;
            while ($row2 = $result->fetch_assoc()) {
                array_push($optionarray, $optioncount);
                array_push($optionlabelarray, $row2['optionlabel']);
                array_push($corrarray, $row2['is_correct']);
                if ($row2['is_correct'] == 1) {
                    array_push($corranswers, $row2['optionlabel']);
                }
                $optioncount++;
            }

            //shuffle option order and display options
            shuffle($optionarray);
            for ($n = 0; $n < $optioncount; $n++) {
                $data .= ('<input type="checkbox" id="MCMAQ' . $count . '-' . ($optionarray[$n] + 1) . '" name="MCMAQ' . $count . '-' . ($optionarray[$n] + 1) . '" value="' . ($corrarray[$optionarray[$n]]) . '" ><label for="MCMAQ' . $count . '-' . ($optionarray[$n] + 1) . '" style="min-width:150px;">&nbsp;&nbsp;' . $optionlabelarray[$optionarray[$n]] . '</label> <br>');
            }

            //append correct answer options
            $data .= <<<EOT
                            </div>
                            <span id="ans$count" style="display:none;">
                        EOT;
            foreach ($corranswers as $y) {
                $data .= $y . ",";
            }
        }

        /* Fill in the blanks */
        if ($questionarray[$count - 1]['question_type'] == "FITBQ") {

            $data .= <<<EOT
                                        <div class="FITBQ" style="padding-left: 15px; font-size: 18px; padding-top: 20px;">
                                        <input name="FITBQ$count" class="form-control FITB$count" style="display:inline; width:350px;" placeholder="Enter your answer"><br>
                                        </div>
                                        <span id="ans$count" style="display:none;">
                                    EOT;

            $sql = $conn->prepare("SELECT * FROM `type_fitbq` WHERE from_question_id = ?");
            $sql->bind_param("s", $questionarray[$count - 1]['question_id']);
            $sql->execute();
            $result = $sql->get_result();
            $sql->close();
            while ($row2 = $result->fetch_assoc()) {
                $data .= ($row2['accepted_answer'] . ",,");
            }
        }

        /* Essay / Constructed Response */
        if ($questionarray[$count - 1]['question_type'] == "ESSAY") {
            $data .= <<<EOT
                            <div class="ESSAY" style="padding-left: 15px; font-size: 18px; padding-top: 20px; margin-bottom: -15px;">
                            <textarea name="ESSAY$count" class="form-control ESSAY$count" placeholder="Type your answer here. Drag the bottom right corner of the text area to toggle its height."
                              style="height:180px; min-height: 90px; min-width:85%; max-width:85%; max-height:396px; white-space: pre-wrap; font-family:monospace; font-size:12.5px;"></textarea><br>
                            </div>
                        EOT;
        }

        $data .= <<<EOT
                                </span>
                            </div>
                        </div>
                    EOT;

        if ($isbacktrack == 2) {
            $data .= "<br><br>";
        }
    }

    /* Retrive the remaining essay questions - only if isshufflequestionorder is set to 2 */
    if ($isshufflequestionorder == 2) {
        $sql = $conn->prepare("SELECT * FROM questions WHERE from_quiz_id = ? AND question_type = 'ESSAY'");
        $sql->bind_param("s", $quizid);
        $sql->execute();
        $questionlist = $sql->get_result();
        $sql->close();

        while ($row = $questionlist->fetch_assoc()) {
            $questionid = $row['question_id'];
            $questiontext = nl2br($row['question_text']);
            $questionpoints = $row['points'];
            $questiontype = $row['question_type'];

            $data .= <<<EOT
                            <div class="question">
                            <div class="row">
                                <div class="count questionnum" style="padding-left: 35px;">
                                    Question <span id="question$count">$count</span> of $numquestions<span style="font-size:17px;">&nbsp; &nbsp;
                                    (<span class="obtainedpoints" style="display:none;"></span><span class="points">$questionpoints</span> points)</span>
                                    <span style="display:none;" class="questiontype">$questiontype</span>
                                    <span style="display:none;" class="questionid">$questionid</span>
                                </div>
                            </div>
                            <div class="count-7" style="padding-left: 60px; font-size: 20px; padding-right:20%">
                                $questiontext
                        EOT;

            /* Essay / Constructed Response */
            $data .= <<<EOT
                            <div class="ESSAY" style="padding-left: 15px; font-size: 18px; padding-top: 20px; margin-bottom: -15px;">
                            <textarea name="ESSAY$count" class="form-control ESSAY$count" placeholder="Type your answer here. Drag the bottom right corner of the text area to toggle its height."
                              style="height:180px; min-height: 90px; min-width:85%; max-width:85%; max-height:396px; white-space: pre-wrap; font-family:monospace; font-size:12.5px;"></textarea><br>
                            </div>
                        EOT;


            $data .= <<<EOT
                                </span>
                            </div>
                        </div>
                    EOT;

            if ($isbacktrack == 2) {
                $data .= "<br><br>";
            }

            $count++;
        }
    }
}

if ($status == 400) {
    $data = "<b>Critical error: Couldn't fetch exam questions! Please contact the quiz maker and developer for details.</b>";
}

//store to JSON object and forward to JS
$retObj = array(
    'status' => $status,
    'data' => $data,
);
$myJSON = json_encode($retObj, JSON_FORCE_OBJECT);
echo $myJSON;

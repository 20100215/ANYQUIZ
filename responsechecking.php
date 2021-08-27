<!--Check if user is logged in-->
<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
if (!isset($_GET["resultid"]) || !isset($_GET["uname"])) {
    header("Location: index.php");
    exit();
}

$resultid = $_GET["resultid"];
$attemptby = $_GET["uname"];

include_once("./src/checkmaintenancestatus.php");
include_once("./src/dbconnect.php");

/* check if result id exits and its from quiz id belongs to the quiz owner, else redirect to home page */
$sql = $conn->prepare("SELECT * FROM results WHERE result_id = ?");
$sql->bind_param("s", ($resultid));
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows == 0) {
    //result does not exist
    header("Location: index.php");
    exit();
} else {
    //search for the quiz to determine owner
    while ($row = $result->fetch_assoc()) {
        $quizid = $row['from_quiz_id'];
        $sql = $conn->prepare("SELECT quiz_title,from_user_id,is_viewquestions,has_essay FROM quizzes WHERE quiz_id = ?");
        $sql->bind_param("s", $quizid);
        $sql->execute();
        $result2 = $sql->get_result();
        while ($row2 = $result2->fetch_assoc()) {
            if ($row2['from_user_id'] != $_SESSION['userid']) {
                //user is not the quiz owner
                header("Location: index.php");
                exit();
            }
            //check if quiz has essays
            $hasessay = $row2['has_essay'];
            if ($hasessay == 0) {
                //no essays to show
                header("Location: index.php");
                exit();
            }
            //get quiz and attempt data
            $quiztitle = $row2['quiz_title'];
            $attemptby = $_GET['uname'];
            $attempttime = $row['attempt_datetime'];
            $partialscore = round($row['score'], 2);
            $perfectscore = $row['perfect_score'];
            $isviewquestions = $row2['is_viewquestions'];
        }
    }
}

$partialpoints = 0;
$ndx = 0;
$questionlist = [];

/* get all responses */
$sql = $conn->prepare("SELECT * FROM essay_responses WHERE from_result_id = ? ORDER BY from_question_id");
$sql->bind_param("s", $resultid);
$sql->execute();
$result = $sql->get_result();
while ($row = $result->fetch_assoc()) {
    $currquestionid = $row['from_question_id'];
    /* get corresponding question info */
    $sql = $conn->prepare("SELECT question_text, points FROM questions WHERE question_id = ?");
    $sql->bind_param("s", $currquestionid);
    $sql->execute();
    $result2 = $sql->get_result();
    while ($row2 = $result2->fetch_assoc()) {
        //details to display (store in array)

        $questionlist[$ndx] = array(
            'questiontext' => $row2['question_text'],
            'maxpoints' => $row2['points'],
            'essayid' => $row['essay_id'],
            'answer' => $row['answer'],
            'points' => $row['points'],
            'feedback' => $row['feedback']
        );

        //add $currpoints to partial essay score - useful when rechecking/regrading with existing score
        if ($row['points'] != -1) {
            $partialpoints += $row['points'];
        }

        $ndx++;
    }
}
$sql->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-theme.css" rel="stylesheet">
    <link href="css/elegant-icons-style.css" rel="stylesheet" />
    <link href="css/font-awesome.min.css" rel="stylesheet" />
    <link href="css/jquery-jvectormap-1.2.2.css" rel="stylesheet">
    <link rel="stylesheet" href="css/fullcalendar.css">
    <link href="css/widgets.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/style-responsive.css" rel="stylesheet" />
    <link href="css/xcharts.min.css" rel=" stylesheet">
    <link href="css/jquery-ui-1.10.4.min.css" rel="stylesheet">
    <link href="css/helptip.css" rel="stylesheet">
    <link rel="icon" href="favicon.ico">
    <meta name="description" content="ANYQUIZ is a fully featured quiz maker for school, business, or just for fun. Create one now and explore its awesome features that match your desire and purpose.">
    <meta name="author" content="Wayne Dayata">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <!-- javascripts -->
    <script src="js/jquery.js"></script>
    <script src="js/jquery-ui-1.10.4.min.js"></script>
    <script src="js/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui-1.9.2.custom.min.js"></script>
    <!-- bootstrap -->
    <script src="js/bootstrap.min.js"></script>

    <!--custome script for all page-->
    <script src="js/scripts.js"></script>

    <!-- nice scroll -->
    <script src="js/jquery.nicescroll.js" type="text/javascript"></script>

    <script type="text/javascript" src="./src/unifiedpopups.js"></script>
    <script type="text/javascript" src="./src/auth.js"></script>

    <link href="css/offline-theme-chrome.css" rel="stylesheet">
    <link href="css/offline-language-english.min.css" rel="stylesheet">
    <script src="js/offline.min.js" type="text/javascript"></script>
    <style>
        .link {
            cursor: pointer;
        }

        .stats {
            margin-left: 75px;
            margin-bottom: 30px;
            font-size: 16px;
            width: 620px;
            padding: 12px 15px;
            background-color: #cce6ff;
            border: 2px solid #81c0ff;
            border-radius: 12px;
        }

        @media (min-width:1100px) {
            .warningcont {
                margin-left: 87px;
            }
        }
    </style>

    <script type="text/javascript" src="./src/responsechecking.js"></script>

    <!--For MathJax expressions-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.1/MathJax.js?config=TeX-MML-AM_CHTML"></script>
    <style>
        .mjx-chtml,
        .MJXc-display {
            display: inline-block !important;
            margin: 4px 1px !important;
        }
        .code {
            font-family:monospace;
            font-size:14px;
        }
    </style>

    <script>
        $(document).ready(function() {
            $('html').getNiceScroll().remove();
            $('html').css({
                "overflow-x": "hidden"
            });
            $(window).keydown(function(event) {
                if (event.keyCode == 13) {
                    if (isbacktrack != 2 && $(".questiontype:eq(" + visibleDiv + ")").html() != "ESSAY") {
                        event.preventDefault();
                        return false;
                    }
                }
            });
            MathJax.Hub.Config({
                extensions: ["tex2jax.js", "TeX/AMSmath.js"],
                jax: ["input/TeX", "output/HTML-CSS"],
                tex2jax: {
                    inlineMath: [
                        ["$$", "$$"]
                    ],
                    processEscapes: true
                }
            });
        });
    </script>

    <title>
        <?php echo $quiztitle; ?> (Checking) | ANYQUIZ
    </title>
</head>

<body>
    <section id="container" class="">


        <header class="header white-bg" style="z-index:5;">
            <div class="toggle-nav">
                <div class="icon-reorder tooltips" data-original-title="Toggle Navigation" data-placement="bottom"><i class="icon_menu"></i></div>
            </div>

            <!--logo start-->

            <a href="index.php" class="logo"><b>ANY <span class="lite">QUIZ</span></b></a>
            <!--logo end-->

            <div class="nav search-row" id="top_menu">
                <!--  search form start -->
                <ul class="nav top-menu">
                    <li>
                        <form class="navbar-form" method="GET" action="quiztaking.php">
                            <input class="form-control" name="quizcode" maxlength="6" placeholder="Search Quiz Code">
                            <!-- hit ENTER to submit the form -->
                        </form>
                    </li>
                </ul>
                <!--  search form end -->
            </div>

            <div class="top-nav notification-row">
                <!-- notificatoin dropdown start-->
                <ul class="nav pull-right top-menu">
                    <!-- user login dropdown start-->
                    <li class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="profile-ava">
                                <img alt="" src="img/id-pic1.png">
                            </span>

                            <!-- User name-->
                            <span class="username">
                                <?php echo $_SESSION['username']; ?>
                            </span>
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu extended logout">
                            <div class="log-arrow-up"></div>
                            <li class="eborder-top">
                                <a href="profile.php"><i class="icon_profile"></i> My Profile</a>
                            </li>
                            <li>
                                <a onclick="signoutClick(event)" style="cursor:pointer;"><i class="icon_key_alt"></i> Log Out</a>
                            </li>
                        </ul>
                    </li>
                    <!-- user login dropdown end -->
                </ul>
                <a class="btn btn-success" href="quiz_maker.php" style=" margin-top: 7px;"><span class="fa fa-plus">&nbsp;&nbsp;</span><b>Create New Quiz</b></a>
                <!-- notificatoin dropdown end-->
            </div>
        </header>
        <!--header end-->

        <!--sidebar start-->
        <aside>
            <div id="sidebar" class="nav-collapse " style="z-index:4;">
                <!-- sidebar menu start-->
                <ul class="sidebar-menu">
                    <li class="sub-menu">
                        <a class="" href="index.php">
                            <i class="icon_house"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="quiz.php" class="">
                            <i class="icon_document"></i>
                            <span>My Quizzes</span>
                        </a>
                    </li>
                    <li class="sub-menu mobile">
                        <a href="quiz_maker.php" class="">
                            <i class="fa fa-plus"></i>
                            <span>Create a Quiz</span>
                        </a>
                    </li>
                    <li class="sub-menu">
                        <a href="quizfetching.php" class="">
                            <i class="icon_pencil_alt"></i>
                            <span>Take a Quiz</span>
                        </a>
                    </li>
                    <li class="sub-menu mobile">
                        <a href="profile.php" class="">
                            <i class="icon_profile"></i>
                            <span>My Profile</span>
                        </a>
                    </li>
                    <li class="sub-menu">
                        <a href="about.php" class="">
                            <i class="icon_info"></i>
                            <span>About</span>
                        </a>
                    </li>
                    <li class="sub-menu mobile">
                        <a onclick="signoutClick(event)" style="cursor:pointer;">
                            <i class="icon_key"></i>
                            <span>Log Out</span>
                        </a>
                    </li>
                </ul>


                <!-- sidebar menu end-->
            </div>
        </aside>
        <!--sidebar end-->

        <!--main content start-->
        <section id="main-content">
            <section class="wrapper">

                <!--overview start-->
                <div class="row">
                    <div class="col-lg-9">
                        <h3 style="margin: 20px; padding-bottom: 10px; font-weight:bold;">
                            <?php echo $quiztitle; ?>
                        </h3>
                        <h5 style="margin: 20px; padding-bottom: 10px; font-weight:bold;">
                            Attempt by: &nbsp; <span style="color:blue;"><?php echo $attemptby; ?></span> &nbsp; &nbsp; &nbsp;
                            Attempt time: &nbsp; <span style="color:blue;"><?php echo $attempttime; ?></span> &nbsp; &nbsp; &nbsp;
                            Current score: &nbsp; <span style="color:blue;"><span id="currentscore"><?php echo $partialscore; ?></span> / <?php echo $perfectscore; ?></span>
                        </h5>
                    </div>
                </div>

                <form id="quiz" method="POST" onsubmit="checkFeedback(event)" autocomplete="off">

                    <!-- questions tab -->
                    <div class="row maintab">
                        <!-- question groups tab - each question will be added and set via PHP/MySQL -->
                        <div class="questions">

                            <?php
                            $count = 1;
                            for ($x = 0; $x < $ndx; $x++) {
                                $questiontext = nl2br($questionlist[$x]['questiontext']);
                                $maxpoints = $questionlist[$x]['maxpoints'];
                                $essayid = $questionlist[$x]['essayid'];
                                $answer = $questionlist[$x]['answer'];
                                $points = $questionlist[$x]['points'] == -1 ? "" : round($questionlist[$x]['points'], 1);
                                $feedback = $questionlist[$x]['feedback'];
                                if ($feedback == "" || $feedback == NULL) {
                                    $display = "style='display:none;'";
                                    $checked = "";
                                } else {
                                    $display = "";
                                    $checked = "checked";
                                }
                                echo <<<EOT
                                        <div class="question">
                                            <div class="row">
                                                <div class="count questionnum" style="padding-left: 45px; margin-bottom:15px;">
                                                    Question <span id="question$count">$count</span><span style="font-size:17px;">&nbsp; &nbsp;
                                                    (<span class="points">$maxpoints</span> points)</span>
                                                </div>
                                                <input type="hidden" name="essayid$count" value="$essayid">
                                            </div>
                                            <div class="count-7" style="padding-left: 60px; font-size: 20px; padding-right:20%; margin-bottom:15px;">
                                                $questiontext
                                                <div class="ESSAY" style="padding-left: 15px; font-size: 18px; padding-top: 20px;">
                                                    <textarea disabled class="form-control ESSAY$count" placeholder="No answer text provided." style="height:135px; min-height: 135px; min-width:85%; max-width:85%; max-height:396px; font-family:monospace; font-size:12.5px; cursor:auto;">$answer</textarea>
                                                </div>
                                            </div>
                                            <div class="count-7 stats">
                                                Points: <input name="points$count" id="points$count" value="$points" class="points form-control toggle" type="number" min="0" max="$maxpoints" step="0.5" style="font-size: 15px; height:30px; margin-left: 5px; width:72px; display:inline;"> &nbsp; <b>/ &nbsp; <span id="maxpoints$count">$maxpoints</span></b>
                                                &nbsp; &nbsp; &nbsp;<label><input type="checkbox" $checked class="togglefeedback$count toggle" onchange="togglefeedback($count)"> Enable answer feedback</label>
                                                <br>
                                                <div class="feedbackbox$count" $display>
                                                    Input answer feedback:
                                                    <div style="display:-webkit-inline-box;">
                                                        <textarea name="feedback$count" class="feedback$count form-control toggle" placeholder="Enter answer feedback (optional)" style="width:400px; height:75px; resize:none; margin-left:16px;">$feedback</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    EOT;
                                $count++;
                            }
                            ?>

                            <!-- end of question groups tab-->
                        </div>

                        <!-- additional info to pass to server -->
                        <input type="hidden" id="numessays" name="numessays" value="<?php echo $ndx; ?>">
                        <input type="hidden" name="resultid" value="<?php echo $resultid; ?>">
                        <input type="hidden" id="partialpoints" name="partialpoints" value="<?php echo $partialpoints; ?>">

                        <div class="row" style="padding-left:60px;">
                            <div style="width:auto; display:inline; float:left; font-size: 15px; font-weight: 700px; margin-right: 20px;">
                                Once you are finished, click <span style="color:blue;"><b>Save and Update</b></span>.
                                <?php
                                if ($isviewquestions == 1) echo "<br>Your scores and feedback will be visible to the attempt owner."
                                ?>
                                <br><br>
                            </div>
                            <div style="float:right; margin-right:25%">
                                <button type="submit" class="btn btn-lg btn-success saveandupdate"><b>Save and Update</b></button>
                            </div>
                            <div style="float:right; margin-right:25%">
                                <button type="button" class="btn btn-lg btn-info editagain" style="display:none;" onclick="editagain()"><b>Edit Scores</b></button>
                            </div>
                        </div>

                        <!-- end of questions tab-->

                    </div>
                </form>

            </section>
            <a href="responselist.php"><button type="button" class="btn btn-info" style="margin-left:40px;"><b>View Pending Responses</b></button>
                <div style="height:50px">
                </div>
        </section>
    </section>

    <!-- pop up alert -->
    <div style="position:fixed; top:80px; padding-left:42px; width:100%">
        <div id="popupalert" class="alert alert-danger" style="border-radius:6px; width:440px; margin:auto; display:none; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">
            <i class='icon_error-circle'></i>&nbsp; &nbsp; <span id="alertcontent"></span>
        </div>
        <div id="popupsuccess" class="alert alert-success" style="border-radius:6px; width:440px; margin:auto; display:none; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">
            <i class='icon_check_alt'></i>&nbsp; &nbsp; <span id="successcontent"></span>
        </div>
        <div id="popupinfo" class="alert alert-success" style="border-radius:6px; width:440px; margin:auto; display:none; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">
            <i class='icon_info'></i>&nbsp; &nbsp; <span id="infocontent"></span>
        </div>
    </div>

</body>

</html>
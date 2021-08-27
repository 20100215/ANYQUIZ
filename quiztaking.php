<!--Check if user is logged in-->
<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
if (!isset($_GET["quizcode"])) {
    header("Location: quizfetching.php");
    exit();
}

include_once("./src/checkmaintenancestatus.php");
include_once("./src/dbconnect.php");

/* check if quiz exists, then prepare quiz details */
$sql = $conn->prepare("SELECT * FROM quizzes WHERE access_code = ?");
$sql->bind_param("s", ($_GET['quizcode']));
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows == 0) {
    $_SESSION["quizfetchfailed"] = 1;
    header("Location: quizfetching.php");
    exit();
} else {

    while ($row = $result->fetch_assoc()) {
        if ($row['is_public'] == 0 && $row['from_user_id'] != $_SESSION['userid']) {
            //quiz exists but not set to public
            $_SESSION["quizfetchfailed"] = 2;
            header("Location: quizfetching.php");
            exit();
        } else {
            unset($_SESSION["quizfetchfailed"]);
            $quizid = $row['quiz_id'];
            $quiztitle = $row['quiz_title'];
            $quizdesc = nl2br($row['quiz_desc']);
            $timelimit = $row['time_limit'];
            $attemptlimit = $row['attempt_limit'];
            $isbacktrack = $row['is_allowbacktrack'];
            $isshufflequestionorder = $row['is_shufflequestionorder'];
            $isexammode = $row['is_exammode'];
            $isviewquestions = $row['is_viewquestions'];
            $isshowcorrectanswers = $row['is_showcorrectanswers'];
            $hasessay = $row['has_essay'];
        }
    }
}

/* get number of questions  and points*/
$sql = $conn->prepare("SELECT COUNT(question_id),SUM(points) FROM questions WHERE from_quiz_id = ?");
$sql->bind_param("s", $quizid);
$sql->execute();
$result = $sql->get_result();
$row = $result->fetch_assoc();
$numquestions = $row['COUNT(question_id)'];
$numpoints = $row['SUM(points)'];

/* get attempts used by user*/
$sql = $conn->prepare("SELECT COUNT(result_id) FROM results WHERE from_quiz_id = ? AND from_user_id = ?");
$sql->bind_param("ss", $quizid, $_SESSION['userid']);
$sql->execute();
$result = $sql->get_result();
while ($row = $result->fetch_assoc()) {
    $attemptsused = $row['COUNT(result_id)'];
}

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

    <link href="css/offline-theme-chrome2.css" rel="stylesheet">
    <link href="css/offline-language-english.min.css" rel="stylesheet">
    <script src="js/offline.min.js" type="text/javascript"></script>
    <style>
        .link {
            cursor: pointer;
        }

        @media (min-width:1100px) {
            .warningcont {
                margin-left: 87px;
            }
        }
        .code {
            font-family:monospace;
            font-size:14px;
        }
    </style>

    <!--For MathJax expressions-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.1/MathJax.js?config=TeX-MML-AM_CHTML"></script>
    <style>
        .mjx-chtml,.MJXc-display{
            display:inline-block !important;
            margin:4px 1px !important;
        }
    </style>
    <script>
        var numquestions = <?php echo $numquestions ?>;
        var isexammode = <?php echo $isexammode ?>;
        var isbacktrack = <?php echo $isbacktrack ?>;
        var timelimit = <?php echo $timelimit ?>;
        var isshufflequestionorder = <?php echo $isshufflequestionorder ?>;
        var isviewquestions = <?php echo $isviewquestions ?>;
        var isshowcorrectanswers = <?php echo $isshowcorrectanswers ?>;
        var hasessay = <?php echo $hasessay ?>;
        var quizid = <?php echo $quizid; ?>;
        var isvalidattempt = <?php echo ($attemptlimit > 0 && $attemptsused >= $attemptlimit) ? 0 : 1; ?>;
        var perfectscore = <?php echo $numpoints ?>;
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
                skipStartupTypeset: true,
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

    <script type="text/javascript" src="./src/quiztaking.js"></script>

    <title>
        <?php echo $quiztitle; ?> | ANYQUIZ
    </title>
</head>

<body onload="checkexammode()">
    <section id="container" class="">


        <header class="header white-bg" style="z-index:5;">
            <div class="toggle-nav">
                <div class="icon-reorder tooltips" data-original-title="Toggle Navigation" data-placement="bottom"><i class="icon_menu"></i></div>
            </div>

            <!--logo start-->

            <a class="logo link" onclick="checkbeforeredirect('index.php')"><b>ANY <span class="lite">QUIZ</span></b></a>
            <!--logo end-->

            <!-- quiz timer -->
            <div style="position:fixed; top:8px; width:100%;">
                <div id="timeralert" class="alert" role="alert" style="padding:9px !important; width:260px; margin:auto; font-size:18px; color:#202326; background-color:#e2e3e5;border-color:grey; border-radius:6px; text-align:center; display:none; z-index:6;">
                    <i class="icon_clock"></i> &nbsp;
                    <span id="timercomment"></span>
                    <b id="timer"></b>
                </div>
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
                                <a onclick="checkbeforeredirect('profile.php')"><i class="icon_profile link"></i> My Profile</a>
                            </li>
                            <li>
                                <a onclick="checksignout(event)" style="cursor:pointer;"><i class="icon_key_alt"></i> Log Out</a>
                            </li>
                        </ul>
                    </li>
                    <!-- user login dropdown end -->
                </ul>

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
                        <a onclick="checkbeforeredirect('index.php')" class="link">
                            <i class="icon_house"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="sub-menu">
                        <a onclick="checkbeforeredirect('quiz.php')" class="link">
                            <i class="icon_document"></i>
                            <span>My Quizzes</span>
                        </a>
                    </li>
                    <li class="sub-menu mobile">
                        <a onclick="checkbeforeredirect('quiz_maker.php')" class="link">
                            <i class="fa fa-plus"></i>
                            <span>Create a Quiz</span>
                        </a>
                    </li>
                    <li class="active">
                        <a onclick="checkbeforeredirect('quizfetching.php')" class="link">
                            <i class="icon_pencil_alt"></i>
                            <span>Take a Quiz</span>
                        </a>
                    </li>
                    <li class="sub-menu mobile">
                        <a onclick="checkbeforeredirect('profile.php')" class="link">
                            <i class="icon_profile"></i>
                            <span>My Profile</span>
                        </a>
                    </li>
                    <li class="sub-menu">
                        <a onclick="checkbeforeredirect('about.php')" class="link">
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
                        <h3 class="quizTitle" style="margin: 20px; padding-bottom: 10px; font-weight:bold;">
                            <?php echo $quiztitle; ?>
                        </h3>
                    </div>
                </div>

                <form id="quiz" method="POST" onsubmit="checkQuiz(event)" autocomplete="off">

                    <!-- quiz settings tab -->
                    <div class="row maintab">
                        <div class="count-4" style="padding-left: 50px; font-size: 20px; font-weight:bold;">
                            Points: &nbsp; <span id="numpoints" style="color:blue"><?php echo $numpoints ?></span>
                            &nbsp; &nbsp; &nbsp; &nbsp;
                            Questions: &nbsp; <span id="numquestions" style="color:blue"><?php echo $numquestions ?></span>
                            &nbsp; &nbsp; &nbsp; &nbsp;
                            Time Limit: &nbsp;
                            <span style="color:blue">
                                <?php
                                if ($timelimit < 0) {
                                    echo "None";
                                } else if ($timelimit < 60) {
                                    echo $timelimit . " mins";
                                } else if ($timelimit >= 60) {
                                    echo (intval($timelimit / 60)) . " hour";
                                    if ($timelimit >= 120) {
                                        echo "s";
                                    }
                                    if ($timelimit % 60 != 0) {
                                        echo ", " . ($timelimit % 60) . " mins";
                                    }
                                }
                                ?>
                            </span>
                            &nbsp; &nbsp; &nbsp; &nbsp;
                            Attempts made: &nbsp;
                            <span style="color:<?php echo ($attemptlimit > 0 && $attemptsused >= $attemptlimit) ? "red" : "blue"; ?>">
                                <?php
                                echo $attemptsused;
                                if ($attemptlimit > 0) {
                                    echo " / " . $attemptlimit;
                                }
                                ?>
                            </span>
                        </div>
                        <div class="count-5" style="padding-left: 50px; padding-top:20px; font-size: 17px;"><b>Instruction:</b><br>
                            <div style="padding-left:25px; padding-right:20%">
                                <?php echo $quizdesc; ?>
                            </div>
                        </div>
                        <div class="count-6" style="padding-top: 25px; padding-left: 50px; font-size: 14px;">
                            <?php
                            if ($isexammode || !$isbacktrack) {
                                if ($isexammode == 1) {
                                    echo "<div class='alert alert-warning' role='alert' style='margin-right:25%; border-radius:12px;'><i class='icon_error-triangle'></i>&nbsp; &nbsp; <b>You must <u>stay within this page only</u> for the entire duration of the quiz. You will not be allowed to continue once a focus change is detected and an invalid attempt will be recorded.</b></div>";
                                }
                                if ($isexammode == 2) {
                                    echo "<div class='alert alert-warning' role='alert' style='margin-right:25%; border-radius:12px;'><i class='icon_error-triangle'></i>&nbsp; &nbsp; <b>You must <u>enable fullscreen mode</u> and <u>stay within this page only</u> during the quiz. You will not be allowed to continue once an attempt to exit full screen or a focus change is detected, and an invalid attempt will be recorded. <br><a style='cursor:pointer;' onclick='togglefullscreen()'>Click here to enable fullscreen mode and begin quiz button.</a></b></div>";
                                }
                                if (!$isbacktrack) {
                                    echo "<div class='alert alert-warning' role='alert' style='margin-right:25%; border-radius:12px;'><i class='icon_error-triangle'></i>&nbsp; &nbsp; <b><u>Backtracking is disabled in this quiz</u>: Once you submit an answer, you won't be able to go back to the previous questions and change your responses.</b></div>";
                                }
                            }
                            if ($attemptlimit > 0 && $attemptsused >= $attemptlimit) {
                                echo "<div class='alert alert-danger' role='alert' style='margin-right:25%; border-radius:12px;'><i class='icon_close_alt'></i>&nbsp; &nbsp; <b>Warning! You have used all your attempts given for this quiz. <br><span class='warningcont'>You may still proceed to take the quiz; however, <u>your answers and results will no longer be recorded</u></b>.</span></div>";
                            }
                            ?>
                        </div>
                        <!-- embed serialized values for transmission to database -->
                        <div class="count" style="margin-right:20%;">
                            <button id="takethequiz" type="button" class="btn btn-info" style=" display:block; margin:auto; text-align:center; width: 200px; height: 40px; border: 1px solid white; right: 150px; 
                            font-size: 15px;" onclick="sendtoken(event)" disabled>
                                <b>Take the Quiz</b>
                            </button>
                        </div>
                        <a href="index.php"><button type="button" class="btn btn-info" style="margin-left:40px;"><b>Back to Home</b></button></a>

                        <!-- end of quiz settings tab-->
                    </div>

                    <!-- questions tab -->
                    <div class="row maintab" id="questionlist" style="display:none;">
                        <!-- question groups tab - each question will be added and set via PHP/MySQL -->
                        <div class="questions">

                            <!-- question fetching transferred to another PHP page - a security update -->

                            <!-- end of question groups tab-->
                        </div>
                        <br><br>

                        <div id="questionbuttons" class="row" style="padding-left: 60px; width:75%; display:none;">
                            Go to question: &nbsp;

                            <?php
                            for ($i = 1; $i <= $numquestions; $i++) {
                                echo <<<EOT
                                    <button type="button" class="questionnumbutton" style="background-color:dodgerblue; color: white; margin-bottom:10px; border-radius:5px;" onclick="showquestion($i)">$i</button>&nbsp;&nbsp;&nbsp;
                                EOT;
                            }
                            ?>
                        </div>
                        <br>
                        <div class="row" style="padding-left: 60px;">
                            <div style="float:left;">
                                <button type="button" id="prevquestion" class="btn btn-info" style="display:none;" onclick="showPrev()"><b>Previous</b></button>
                                <button type="button" id="nextquestion" class="btn btn-info" style="display:none;" onclick="showNext()"><b>Next</b></button>
                            </div>
                            <div style="float:right; margin-right:25%">
                                <input type="hidden" name="attempttime" value="0">
                                <input type="hidden" name="totalscore" value="0">
                                <input type="hidden" name="perfectscore" value="0">
                                <input type="hidden" name="isfinal" value="1">
                                <button id="finishquiz" type="submit" class="btn btn-success"><b>Finish Quiz</b></button>
                                <button id="viewresults" type="button" class="btn btn-success" onclick="showtab(1,2)" style="display:none;"><b>View Results</b></button>
                            </div>
                        </div>
                        <!-- end of questions tab-->
                        <br>
                    </div>

                </form>

                <!-- end screen tab -->
                <div class="row maintab" style="display:none;">
                    <div class="row">
                        <div class="count" style="padding-left:50px;"> &nbsp;Score: &nbsp;<span id="totalscore" style="color:blue;"></span></div>
                    </div>
                    <div class="row">
                        <div class="count" style="padding-left:50px;"> &nbsp;Attempt time: &nbsp;<span id="attempttime" style="color:blue;"></span></div>
                    </div>
                    <div class="row">
                        <div class="count" style="padding-left:50px;"> &nbsp;Questions answered: &nbsp;<span id="questionsanswered" style="color:blue;"></span></div>
                    </div>
                    <div class="row">
                        <?php
                        if ($isviewquestions == 0) {
                            echo <<<EOT
                                        <div class='alert alert-warning' role='alert' style='width:550px; margin-left:50px;'><i class='icon_error-circle'></i>&nbsp; &nbsp;<b>Viewing of questions after submission is disabled for this quiz.</b></div>
                                    EOT;
                        }
                        if ($isviewquestions == 1 && $isshowcorrectanswers == 0) {
                            echo <<<EOT
                                        <div class='alert alert-warning' role='alert' style='width:550px; margin-left:50px;'><i class='icon_error-circle'></i>&nbsp; &nbsp;<b>Correct answers are hidden.</b></div>
                                    EOT;
                        }
                        if ($attemptlimit > 0) {
                            if ($attemptsused >= $attemptlimit) {
                                echo <<<EOT
                                        <div class='alert alert-danger' role='alert' style='width:550px; margin-left:50px;'><i class='icon_close_alt'></i>&nbsp; &nbsp;<b>You have exceeded the attempt limit for this quiz. Your answers and results for this attempt and the succeeding attempts on this quiz <u>will not be recorded</u>.</b></div>
                                    EOT;
                            } else if ($attemptsused + 1 >= $attemptlimit) {
                                echo <<<EOT
                                        <div class='alert alert-warning' role='alert' style='width:550px; margin-left:50px;'><i class='icon_error-circle'></i>&nbsp; &nbsp;<b>You have used your final attempt for this quiz. You may still opt to take this quiz again; however, your answers and results for the succeeding attempts on this quiz <u>will no longer be recorded</u>.</b></div>
                                    EOT;
                            }
                        }
                        echo <<<EOT
                                    <div id="essaywarning" class='alert alert-info' role='alert' style='display:none; width:550px; margin-left:50px;'><i class='icon_error-circle'></i>&nbsp; &nbsp;<b>Some items are not yet checked.</b></div>
                                EOT;
                        ?>
                    </div>
                    <div class="row">
                        <div class="count-10" style="font-size: 16px; padding-left: 60px; font-weight: 700px;">
                            <?php echo ($attemptlimit > 0 && $attemptsused >= $attemptlimit) ? "" : "Your quiz results have been recorded.<br>"; ?>
                            <?php echo ($isviewquestions == 0) ? "" : "Click the View Questions button below to review your quiz attempt.<br>"; ?>
                            When finished, click Back to Home button to return to the home page.<br><br>
                        </div>
                    </div>
                    <div class="row" style="padding-left: 60px; font-size:18px;">
                        <div style="float:left;">
                            <button type="button" id="viewquestions" class="btn btn-success" onclick="showtab(2,1)"><b>View Questions</b></button>
                        </div>
                        <div style="float:right; margin-right:25%">
                            <a href="index.php"><button type="button" class="btn btn-success"><b>Back to Home</b></button></a>
                        </div>
                    </div>
                    <!-- end of end screen tab -->
                </div>

            </section>
            <div style="height:50px">
            </div>
        </section>
    </section>

    <!-- pop up alert when attempting to click other links during a quiz attempt -->
    <div style="position:fixed; top:80px; padding-left:42px; width:100%">
        <div id="popuperror" class="alert alert-danger" style="border-radius:6px; width:440px; margin:auto; display:none; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">
            <i class='icon_error-circle'></i>&nbsp; &nbsp; Navigation to other pages is disabled while a quiz is ongoing.
        </div>
        <div id="popupalert" class="alert alert-danger" style="border-radius:6px; width:440px; margin:auto; display:none; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">
            <i class='icon_error-circle'></i>&nbsp; &nbsp; <span id="alertcontent"></span>
        </div>
        <div id="popupsuccess" class="alert alert-info" style="border-radius:6px; width:245px; margin:auto; display:none; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">
            <i class='icon_info'></i>&nbsp; &nbsp; <span id="successcontent"></span>
        </div>
    </div>

    <!-- modals to display in replacement of JS alert messages which caused infinite loop issue-->
    <div id="changetaberror" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLongTitle" style="color:maroon; font-weight:bold"><i class="icon_error-circle"></i>&nbsp; &nbsp; Invalid attempt - Focus change detected</h4>
            </div>
            <div class="modal-content" style="padding:15px 20px; box-shadow:none; -webkit-box-shadow:none;">
                <b>You have navigated away from the quiz taking page, thus you are not allowed to continue the quiz and an invalid attempt will be recorded.
                    Click OK to continue.</b>
            </div>
            <div class="modal-footer" style="margin:0">
                <button type="button" class="btn btn-primary" data-dismiss="modal"><b>OK</b></button>
            </div>
        </div>
    </div>

    <div id="fullscreenerror" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLongTitle" style="color:maroon; font-weight:bold"><i class="icon_error-circle"></i>&nbsp; &nbsp; Invalid attempt - Exit full screen detected</h4>
            </div>
            <div class="modal-content" style="padding:15px 20px; box-shadow:none; -webkit-box-shadow:none;">
                <b>You have exited full screen which is not allowed while the quiz session is still ongoing, thus you are not allowed to continue the quiz and an invalid attempt will be recorded.
                    Click OK to continue.</b>
            </div>
            <div class="modal-footer" style="margin:0">
                <button type="button" class="btn btn-primary" data-dismiss="modal"><b>OK</b></button>
            </div>
        </div>
    </div>

    <div id="outoftimeerror" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLongTitle" style="color:#34aadc; font-weight:bold"><i class="icon_error-circle"></i>&nbsp; &nbsp; Time is up!</h4>
            </div>
            <div class="modal-content" style="padding:15px 20px; box-shadow:none; -webkit-box-shadow:none;">
                <b>You have used up the time alloted for your quiz. Your answers are now being submitted. Click OK to continue.</b>
            </div>
            <div class="modal-footer" style="margin:0">
                <button type="button" class="btn btn-primary" data-dismiss="modal"><b>OK</b></button>
            </div>
        </div>
    </div>

</body>

</html>
<!--Check if user is logged in-->
<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
if (!isset($_GET["resultid"])) {
    header("Location: index.php");
    exit();
}

$resultid = $_GET["resultid"];

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
        if ($row['from_user_id'] != $_SESSION['userid']) {
            //user is not the result owner
            header("Location: index.php");
            exit();
        }
        $quizid = $row['from_quiz_id'];
        $sql = $conn->prepare("SELECT quiz_title, is_viewquestions, has_essay FROM quizzes WHERE quiz_id = ?");
        $sql->bind_param("s", $quizid);
        $sql->execute();
        $result2 = $sql->get_result();
        while ($row2 = $result2->fetch_assoc()) {
            //check if quiz has essays and if responses are viewable
            $hasessay = $row2['has_essay'];
            if ($hasessay == 0) {
                //no essays to show
                header("Location: index.php");
                exit();
            }
            $isviewquestions = $row2['is_viewquestions'];
            if ($isviewquestions == 0) {
                //viewing of questions disabled
                header("Location: index.php");
                exit();
            }

            /* get total number of questions */
            $sql = $conn->prepare("SELECT COUNT(question_id) FROM questions WHERE from_quiz_id = ?");
            $sql->bind_param("s", $quizid);
            $sql->execute();
            $result = $sql->get_result();
            while ($row3 = $result->fetch_assoc()) {
                $totalquestions = $row3['COUNT(question_id)'];
            }

            //get quiz and attempt data
            $quiztitle = $row2['quiz_title'];
            $attempttime = $row['attempt_datetime'];
            $duration = $row['duration'];
            $score = round($row['score'], 2);
            $perfectscore = $row['perfect_score'];
            $isfinal = $row['is_final'];
        }
    }
}

if ($isfinal == 0) {
    $scorecomment = $score . " / " . $perfectscore . " &nbsp; (Partial)";
} else {
    $scorecomment = $score . " / " . $perfectscore . " &nbsp; (" . intval($score * 100 / $perfectscore) . "%)";
}

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
            'questionnum' => $row['question_no_label'],
            'questiontext' => $row2['question_text'],
            'maxpoints' => $row2['points'],
            'answer' => $row['answer'],
            'points' => $row['points'],
            'feedback' => $row['feedback']
        );

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

        .code {
            font-family:monospace;
            font-size:14px;
        }
    </style>

    <!--For MathJax expressions-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.1/MathJax.js?config=TeX-MML-AM_CHTML"></script>
    <style>
        .mjx-chtml,
        .MJXc-display {
            display: inline-block !important;
            margin: 4px 1px !important;
        }
    </style>
    <script>
        $(document).ready(function() {
            $('html').getNiceScroll().remove();
            $('html').css({
                "overflow-x": "hidden"
            });
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
    </script>

    <title>View Feedback | ANYQUIZ</title>
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
                    <li class="sub-menu">
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
                            Attempt time: &nbsp; <span style="color:blue;"><?php echo $attempttime; ?></span> &nbsp; &nbsp; &nbsp;
                            Duration: &nbsp; <span style="color:blue;"><?php echo $duration; ?></span> &nbsp; &nbsp; &nbsp;
                            Score: &nbsp; <span style="color:blue;"><?php echo $scorecomment; ?></span>
                        </h5>
                    </div>
                </div>


                <!-- questions tab -->
                <div class="row maintab">
                    <!-- question groups tab - each question will be added and set via PHP/MySQL -->
                    <div class="questions">

                        <?php
                        $count = 1;
                        for ($x = 0; $x < $ndx; $x++) {
                            $questionnum = $questionlist[$x]['questionnum'];
                            $questiontext = nl2br($questionlist[$x]['questiontext']);
                            $maxpoints = $questionlist[$x]['maxpoints'];
                            $answer = $questionlist[$x]['answer'];
                            $points = $questionlist[$x]['points'] == -1 ? "Not yet graded" : round($questionlist[$x]['points'], 1);
                            $feedback = $questionlist[$x]['feedback'];
                            if ($feedback == "" || $feedback == NULL) {
                                $display = "display:none;";
                            } else {
                                $display = "";
                            }
                            echo <<<EOT
                                        <div class="question">
                                            <div class="row">
                                                <div class="count questionnum" style="padding-left: 45px; margin-bottom:15px;">
                                                    Question $questionnum of $totalquestions<span style="font-size:17px;">&nbsp; &nbsp;
                                                    ( <span class="obtainedpoints">$points</span> / <span class="points">$maxpoints</span> points )</span>
                                                </div>
                                            </div>
                                            <div class="count-7" style="padding-left: 60px; font-size: 20px; padding-right:20%; margin-bottom:15px;">
                                                $questiontext
                                                <div class="ESSAY" style="padding-left: 15px; font-size: 18px; padding-top: 20px;">
                                                    <textarea disabled class="form-control ESSAY$count" placeholder="No answer text provided." style="height:135px; min-height: 135px; min-width:85%; max-width:85%; max-height:396px; font-family:monospace; font-size:12.5px; cursor:auto;">$answer</textarea>
                                                </div>
                                            </div>
                                            <div class='alert alert-info comment' role='alert' style='$display width:550px; margin:20px auto 15px 75px; padding: 10px 15px; border-radius:10px;'><i class='icon_info'></i>&nbsp; &nbsp;<b>Answer feedback: &nbsp; </b>$feedback</div>
                                            <br>
                                        </div>
                                    EOT;
                            $count++;
                        }
                        ?>

                        <!-- end of question groups tab-->
                    </div>

                    <!-- additional info to pass to server -->

                    <div class="row" style="padding-left:60px;">
                        <div style="width:auto; display:inline; float:left; font-size: 15px; font-weight: 700px; margin-right: 20px;">
                            Only constructed response questions and answers are permanently stored and viewable by users.
                        </div>
                    </div>

                    <!-- end of questions tab-->
                </div>

            </section>
            <a href="index.php"><button type="button" class="btn btn-info" style="margin-left:40px;"><b>Back to Dashboard</b></button>
                <div style="height:50px">
                </div>
        </section>
    </section>

</body>

</html>
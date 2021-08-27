<!--Check if user is logged in-->
<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
if (!isset($_GET["quizid"])) {
    header("Location: quiz.php");
    exit();
}

include_once("./src/checkmaintenancestatus.php");
include_once("./src/dbconnect.php");
$quiztitle = $_GET['quiztitle'];
$quizid = $_GET['quizid'];
$quizcode = $_GET['quizcode'];

/* verify if the quiz exist and if the user owns the quiz */
$sql = $conn->prepare("SELECT * FROM quizzes WHERE `quiz_id` = ? AND `quiz_title` = ? AND `access_code` = ?");
$sql->bind_param("sss", $quizid, $quiztitle, $quizcode);
$sql->execute();
$result = $sql->get_result();
$sql->close();
if ($result->num_rows == 0) {
    header("Location: quiz.php");
    exit();
} else {
    while ($row = $result->fetch_assoc()) {
        if ($row['from_user_id'] != $_SESSION['userid']) {
            header("Location: quiz.php");
            exit();
        } else {
            $quiztitle = $row['quiz_title'];
            $quizdesc = $row['quiz_desc'];
            $timelimit = $row['time_limit'];
            $attemptlimit = $row['attempt_limit'];
            $isbacktrack = $row['is_allowbacktrack'];
            $isshufflequestionorder = $row['is_shufflequestionorder'];
            $isexammode = $row['is_exammode'];
            $accessibility = $row['is_public'];
            $isviewquestions = $row['is_viewquestions'];
            $isshowcorrectanswers = $row['is_showcorrectanswers'];
            $quizcode = $row['access_code'];
            $hasessay = $row['has_essay'];
        }
    }
}

/* get number of questions */
$sql = $conn->prepare("SELECT COUNT(question_id) FROM questions WHERE from_quiz_id = ?");
$sql->bind_param("s", $quizid);
$sql->execute();
$result = $sql->get_result();
while ($row = $result->fetch_assoc()) {
    $numquestions = $row['COUNT(question_id)'];
}

/* get number of points*/
$sql = $conn->prepare("SELECT SUM(points) FROM questions WHERE from_quiz_id = ?");
$sql->bind_param("s", $quizid);
$sql->execute();
$result = $sql->get_result();
while ($row = $result->fetch_assoc()) {
    $numpoints = $row['SUM(points)'];
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Edit Quiz Settings | ANYQUIZ</title>
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
    <script type="text/javascript" src="./src/quizedit.js"></script>

    <link href="css/offline-theme-chrome.css" rel="stylesheet">
    <link href="css/offline-language-english.min.css" rel="stylesheet">
    <script src="js/offline.min.js" type="text/javascript"></script>
</head>

<body>
    <section id="container" class="">

        <header class="header white-bg">
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
            <div id="sidebar" class="nav-collapse ">
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
                <div class="row">
                    <div class="col-lg-12">
                        <h3 style="margin: 20px; padding-bottom: 10px; font-weight:bold;">
                            <i class="icon_document"></i>&nbsp; &nbsp; &nbsp;Edit quiz settings: &nbsp;
                            <span id="quizTitle"> <?php echo $quiztitle ?> </span>
                        </h3>
                    </div>
                </div>

                <form id="quiz" method="POST" onsubmit="checkandupdate(event)" autocomplete="off">

                    <input type="hidden" name="quizid" value="<?php echo $quizid; ?>">

                    <!-- quiz editing tab -->
                    <div class="row maintab">

                        <div class="count-4" style="padding-left: 50px; font-size: 20px; font-weight:bold;">
                            Points: &nbsp; <span id="numpoints" style="color:blue"><?php echo $numpoints ?></span>
                            &nbsp; &nbsp; &nbsp; &nbsp;
                            Questions: &nbsp; <span id="numquestions" style="color:blue"><?php echo $numquestions ?></span>
                            &nbsp; &nbsp; &nbsp; &nbsp;
                            Quiz code: &nbsp; <span id="numquestions" style="color:blue"><?php echo $quizcode ?></span>
                            &nbsp; &nbsp; &nbsp; &nbsp;
                        </div>

                        <div class="count-4" style="padding-top:25px; padding-left: 50px; font-size: 18px;">Quiz Title:
                            <input name="quizname" value="<?php echo $quiztitle ?>" id="quizname" class="form-control" placeholder="Quiz Title (required)" style="width:350px; left:100px;">
                        </div>

                        <div class="count-5" style="padding-left: 50px; margin-bottom:-10px; font-size: 18px;">Quiz Description:
                            <textarea name="quizdescription" id="quizdescription" class="form-control" placeholder="Quiz Description (required)" style="width:500px; height:155px; min-height: 35px; min-width:320px; max-width:650px; max-height:177px; left:160px;"><?php echo $quizdesc ?></textarea>
                        </div>

                        <div class="count-6" style="padding-left: 50px; font-size: 20px;">
                            <b>Quiz Settings:</b>
                        </div>

                        <div class="count-7" style="padding-top: 15px; padding-left: 60px; font-size: 18px;">
                            Time limit:
                            <select name="istimelimit" id="istimelimit" style="font-size: 15px; margin-left: 5px;" onchange="toggletimelimit()">
                                <option value="1" <?php if ($timelimit != -1) {
                                                        echo "selected='selected'";
                                                    } ?>>Enable</option>
                                <option value="-1" <?php if ($timelimit == -1) {
                                                        echo "selected='selected'";
                                                    } ?>>Disable</option>
                            </select> -
                            <input name="timelimit" id="timelimit" value="<?php if ($timelimit != -1) {
                                                                                echo $timelimit;
                                                                            } ?>" style="font-size: 15px; margin-left: 5px; width:60px;" type="number" min="1" max="180">
                            Minutes
                        </div>

                        <div class="count-7" style="padding-top: 12px; padding-left: 60px; font-size: 18px;">
                            Limit number of attempts per user:
                            <select name="isattemptlimit" id="isattemptlimit" style="font-size: 15px; margin-left: 5px;" onchange="toggleattemptlimit()">
                                <option value="1" <?php if ($attemptlimit != -1) {
                                                        echo "selected='selected'";
                                                    } ?>>Enable</option>
                                <option value="-1" <?php if ($attemptlimit == -1) {
                                                        echo "selected='selected'";
                                                    } ?>>Disable</option>
                            </select> -
                            <input name="attemptlimit" id="attemptlimit" value="<?php if ($attemptlimit != -1) {
                                                                                    echo $attemptlimit;
                                                                                } ?>" style="font-size: 15px; margin-left: 5px; width:60px;" type="number" min="1" max="10">
                            Attempts
                        </div>

                        <div class="count-8" style="padding-top: 12px; padding-left: 60px; font-size: 18px;">
                            Show questions:
                            <select name="isbacktrack" id="isbacktrack" style="font-size: 15px; margin-left: 5px;">
                                <option value="2" <?php if ($isbacktrack == 2) {
                                                        echo "selected='selected'";
                                                    } ?>>All items at once</option>
                                <option value="1" <?php if ($isbacktrack == 1) {
                                                        echo "selected='selected'";
                                                    } ?>>One at a time, backtracking enabled</option>
                                <option value="0" <?php if ($isbacktrack == 0) {
                                                        echo "selected='selected'";
                                                    } ?>>One at a time, backtracking disabled</option>
                            </select>
                        </div>

                        <div class="count-9" style="padding-top: 12px; padding-left: 60px; font-size: 18px;">
                            Question order:
                            <select name="isshufflequestionorder" id="isshufflequestionorder" style="font-size: 15px; margin-left: 5px;">
                                <option value="0" <?php if ($isshufflequestionorder == 0) {
                                                        echo "selected='selected'";
                                                    } ?>>Display all items as is</option>
                                <?php 
                                    if ($hasessay == 1){
                                        echo "<option value='2'";
                                        if ($isshufflequestionorder == 2) echo "selected='selected'";
                                        echo ">Shuffle non-essay items only; display essay items at last segment of quiz</option>";
                                    }
                                ?>
                                <option value="1" <?php if ($isshufflequestionorder == 1) {
                                                        echo "selected='selected'";
                                                    } ?>>Shuffle all items in quiz</option>
                                
                            </select>
                        </div>

                        <div class="row" style="padding-top:12px; padding-left:75px;">
                            <div style="font-size: 18px; width:auto; display:inline; float:left;">
                                Exam Mode:
                                <select name="isexammode" id="isexammode" style="font-size: 15px; margin-left: 5px;" onchange="toggleexammode()">
                                    <option value="1" <?php if ($isexammode == 1) {
                                                            echo "selected='selected'";
                                                        } ?>>Level 1</option>
                                    <option value="2" <?php if ($isexammode == 2) {
                                                            echo "selected='selected'";
                                                        } ?>>Level 2</option>
                                    <option value="0" <?php if ($isexammode == 0) {
                                                            echo "selected='selected'";
                                                        } ?>>Disable</option>
                                </select><br>
                            </div>
                            <div class="exammodetext" style="font-size:13px; width:auto; display:inline; float:left; padding-left:30px; padding-top:5px; color:blue;">
                                When exam mode is enabled, the users are required to stay on the page<br>
                                during the quiz taking session. If a focus change is detected, the session<br>
                                interrupts and an incomplete attempt will be recorded.
                            </div>
                        </div>

                        <div class="count-11" style="padding-top: 18px; padding-left: 60px; font-size: 18px;">
                            Accessibility of this quiz:
                            <select id="accessibility" name="accessibility" style="font-size: 15px; margin-left: 5px;">
                                <option value="0" <?php if ($accessibility == 0) {
                                                        echo "selected='selected'";
                                                    } ?>>Private (only me)</option>
                                <option value="1" <?php if ($accessibility == 1) {
                                                        echo "selected='selected'";
                                                    } ?>>Public (via quiz code)</option>
                            </select>
                        </div>

                        <div class="count-12" style="padding-top: 12px; padding-left: 60px; font-size: 18px;">
                            Allow viewing of questions after quiz:
                            <select id="isviewquestions" name="isviewquestions" style="font-size: 15px; margin-left: 5px;" onchange="toggleviewcorrectanswers()">
                                <option value="1" <?php if ($isviewquestions == 1) {
                                                        echo "selected='selected'";
                                                    } ?>>Yes</option>
                                <option value="0" <?php if ($isviewquestions == 0) {
                                                        echo "selected='selected'";
                                                    } ?>>No</option>
                            </select>
                        </div>

                        <div class="count-13" style="padding-top: 12px; padding-left: 60px; font-size: 18px;">
                            Show correct answers after quiz:
                            <select id="isshowcorrectanswers" name="isshowcorrectanswers" style="font-size: 15px; margin-left: 5px;">
                                <option value="1" <?php if ($isshowcorrectanswers == 1) {
                                                        echo "selected='selected'";
                                                    } ?>>Yes</option>
                                <option value="0" <?php if ($isshowcorrectanswers == 0) {
                                                        echo "selected='selected'";
                                                    } ?>>No</option>
                            </select>
                        </div>

                        <input type="hidden" name="userid" value="<?php echo $_SESSION['userid']; ?>">

                        <div class="row" style="margin-top:40px; padding-left:75px;">
                            <div style="width:auto; display:inline; float:left; font-size: 15px; font-weight: 700px;">
                                Once you are finished, click <span style="color:blue;"><b>Save and Update</b></span>.<br><br>
                            </div>
                            <div style="float:right; margin-right:25%">
                                <button type="submit" class="btn btn-lg btn-success saveandupdate"><b>Save and Update</b></button>
                            </div>
                            <div style="float:right; margin-right:25%">
                                <button type="button" class="btn btn-lg btn-success editagain" style="display:none;" onclick="editagain()"><b>Edit Settings</b></button>
                            </div>
                        </div>

                    </div>
                </form>
            </section>
            <a href="<?php echo "quizinfo.php?quiztitle=" . $quiztitle . "&quizid=" . $quizid . "&quizcode=" . $quizcode; ?>"><button class="btn btn-info" style="margin-left:40px;"><b>Back to View Quiz</b></button></a>
            <div style="height:50px">
            </div>
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

    </section>

    <script>
        $('document').ready(function() {
            $('html').getNiceScroll().remove();
            $('html').css({
                "overflow-x": "hidden"
            });
        });
    </script>
</body>

</html>
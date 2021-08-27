<!--Check if user is logged in-->
<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
if (!isset($_GET["quizcode"])) {
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
        }
        $ispublic = $row['is_public'];
        $hasessay = $row['has_essay'];
    }
}

$sql = $conn->prepare("SELECT * FROM results WHERE `from_quiz_id` = ? ORDER BY attempt_datetime DESC");
$sql->bind_param("s", $quizid);
$sql->execute();
$result = $sql->get_result();
$sql->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Quizzes | ANYQUIZ</title>
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

    <script type="text/javascript" src="./src/auth.js"></script>
    <script type="text/javascript" src="./src/quizedit.js"></script>
    <script type="text/javascript" src="./src/unifiedpopups.js"></script>
    <style>
        td {
            padding: 7px 15px 5px !important
        }
    </style>
    <script>
        $(document).ready(function() {
            $('html').getNiceScroll().remove();
            $('html').css({
                "overflow-x": "hidden"
            });
        });
        var ispublic = <?php echo $ispublic; ?>;
    </script>

    <link href="css/offline-theme-chrome.css" rel="stylesheet">
    <link href="css/offline-language-english.min.css" rel="stylesheet">
    <script src="js/offline.min.js" type="text/javascript"></script>
</head>

<body>
    <section id="container" class="">


        <header class="header white-bg " style="z-index:5;">
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
                    <div class="col-lg-12">
                        <h3 style="margin: 20px; padding-bottom: 10px; font-weight:bold;">
                            <i class="icon_document"></i>&nbsp; &nbsp; &nbsp;View quiz: &nbsp;
                            <span id="quizTitle"> <?php echo $quiztitle ?> </span>
                        </h3>
                    </div>
                    <div class="col-lg-12">
                        <h4 id="instructions" style="margin: 20px; padding-left: 20px; font-weight:bold;">
                            Quiz code: &nbsp; <span id="quizcode" style="color:maroon;"> <?php echo $quizcode ?> </span> &nbsp; &nbsp; &nbsp;
                            <form id="quiz" method="GET" action="quiztaking.php" style="display:inline;">
                                <button type="submit" name="quizcode" value="<?php echo $quizcode ?>" class="btn btn-info"><b>Launch Quiz</b></button>
                            </form>
                            &nbsp; &nbsp; &nbsp;
                            <form id="editquiz" method="GET" action="quizedit.php" style="display:inline;">
                                <input type="hidden" name="quizid" value="<?php echo $quizid ?>">
                                <input type="hidden" name="quiztitle" value="<?php echo $quiztitle ?>">
                                <input type="hidden" name="quizcode" value="<?php echo $quizcode ?>">
                                <button type="submit" class="btn btn-info"><b>Edit Quiz Settings</b></button>
                            </form>
                            &nbsp; &nbsp; &nbsp;
                            <button type="button" class="btn btn-info" style="font-weight:bold;" onclick="copyquizlink()">Copy Quiz Link</button>
                        </h4>
                    </div>
                </div>

                <!--Project Activity start-->
                <section class="panel" style="margin-left:30px; margin-right:30px; max-width:960px; border:2px solid #e6e6e6;">
                    <div class="panel-body progress-panel" style="border:1px; padding:15px 15px 10px;">
                        <div class="row">

                            <?php
                            echo <<<EOT
                            <div class="col-lg-8 task-progress pull-left">
                                <h1><b>Attempt history for this quiz:</b></h1>
                            </div>
                            EOT;

                            /* Check if there are valid attempts. If there are none, do not show stat */
                            $sql = $conn->prepare("SELECT COUNT(score) FROM results WHERE `from_quiz_id` = ? AND `score` >= 0 AND `is_final` = '1'");
                            $sql->bind_param("s", $quizid);
                            $sql->execute();
                            $result3 = $sql->get_result();
                            $sql->close();
                            $data = $result3->fetch_assoc();
                            if ($data['COUNT(score)'] != 0) {
                                /* query min, mean, max scores */
                                $sql = $conn->prepare("SELECT MIN(score),AVG(score),MAX(score) FROM results WHERE `from_quiz_id` = ? AND `score` >= 0 AND `is_final` = '1'");
                                $sql->bind_param("s", $quizid);
                                $sql->execute();
                                $result3 = $sql->get_result();
                                $sql->close();
                                while ($data = $result3->fetch_assoc()) {
                                    $low = round($data['MIN(score)'], 2);
                                    $mean = round($data['AVG(score)'], 2);
                                    $high = round($data['MAX(score)'], 2);
                                }
                                echo <<<EOT
                                        <div class="task-progress" style="margin-right:15px; float:right;">
                                            <h1><b>Low: $low &nbsp; &nbsp; &nbsp; Mean: $mean &nbsp; &nbsp; &nbsp; High: $high</b><h1>
                                        </div>
                                    EOT;
                            }

                            ?>

                        </div>
                    </div>

                    <table class="table table-hover personal-task" style="margin-bottom:4px;">
                        <?php
                        if ($result->num_rows > 0) {
                            echo <<<EOT
                                    <thead>
                                    <tr>
                                        <td><b>#</b></td>
                                        <td><b>Attempt by</b></td>
                                        <td><b>Attempt time</b></td>
                                        <td><b>Duration</b></td>
                                        <td><b>Score</b></td>
                                        <td></td>
                                        </tr>
                                    </thead>
                                EOT;
                        }

                        ?>


                        <tbody>
                            <?php
                            $count = 1;
                            $haspending = 0;
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    //get quiz name
                                    $sql = $conn->prepare("SELECT username FROM users WHERE `user_id` = ?");
                                    $sql->bind_param("s", $row['from_user_id']);
                                    $sql->execute();
                                    $result2 = $sql->get_result();
                                    $sql->close();
                                    $row2 = $result2->fetch_assoc();
                                    $username = $row2['username'];
                                    $username = ($username == $_SESSION['username']) ? "Me" : $username;
                                    $attempttime = $row['attempt_datetime'];
                                    $resultid = $row['result_id'];
                                    $duration = $row['duration'];
                                    $score = round($row['score'], 2);
                                    $perfectscore = $row['perfect_score'];
                                    $isfinal = $row['is_final'];

                                    //check the score for status
                                    if ($score == -2) {
                                        $scorecomment = "Unfinished attempt";
                                    } else if ($score == -1) {
                                        $scorecomment = "Invalid attempt";
                                    } else {
                                        if ($hasessay == 0 || $isfinal == 1) {
                                            $scorecomment = $score . " / " . $perfectscore . " (" . intval($score * 100 / $perfectscore) . "%)";
                                        } else {
                                            $scorecomment = $score . " / " . $perfectscore . " (Partial)";
                                        }
                                    }

                                    //check response status to determine button type
                                    if ($hasessay == 1) {
                                        if ($isfinal == 1) {
                                            $btntype = "secondary";
                                            $btnmsg = "Recheck";
                                            $remark = "";
                                        } else {
                                            $btntype = "warning";
                                            $btnmsg = "Check";
                                            $remark = "*";
                                            $haspending = 1;
                                        }
                                        if($score < 0){
                                            $disabled = "disabled";
                                        } else {
                                            $disabled = "";
                                        }
                                        $responseform = <<<EOT
                                                            <form id="checkresponse$count" method="GET" action="responsechecking.php">
                                                                <input type="hidden" name="resultid" value="$resultid">
                                                                <input type="hidden" name="uname" value="$username">
                                                            </form>
                                        EOT;
                                        $responsebutton = <<<EOT
                                                              <button type="button" $disabled onclick="document.getElementById('checkresponse$count').submit()" class="btn btn-$btntype btn-sm" style="padding:3px 9px; width:66px; margin-top:-3px; margin-right:5px;"><b>$btnmsg</b></button>
                                                          EOT;
                                    } else {
                                        $responseform = "";
                                        $responsebutton = "";
                                        $remark = "";
                                    }

                                    echo <<<EOT
                                            <tr>
                                            <td>$count$remark</td>
                                            <td>$username</td>
                                            <td>$attempttime</td>
                                            <td>$duration</td>
                                            <td>$scorecomment</td>
                                            <td>
                                                $responseform
                                                <form id="deleteattempt$count" method="get" onsubmit="deleteattempt(event,$count)" style="display:inline;">
                                                    <input type="hidden" name="quiztitle" value="$quiztitle">
                                                    <input type="hidden" name="quizid" value="$quizid">
                                                    <input type="hidden" name="quizcode" value="$quizcode">
                                                    <input type="hidden" name="resultid" value="$resultid">
                                                    $responsebutton
                                                    <button type="submit" class="btn btn-danger btn-sm" style="padding:3px 9px; width:66px; margin-top:-3px;"><b>Remove</b></button>
                                                </form>
                                            </td>
                                            </tr>
                                          EOT;
                                    $count++;
                                }
                                if ($haspending == 1) {
                                    echo <<<EOT
                                          <tr>
                                            <td colspan="6" style="font-size:11px;" align="left"><b>*Some items not yet graded; not included in score statistics above</b></td>
                                          <tr>
                                          EOT;
                                }
                            } else {
                                echo <<<EOT
                                        <tr style="padding-bottom:10px;">
                                           <td colspan="5" style="text-align:center;">
                                               <img src="./img/noattempt.png">
                                               <h4><b>No attempts made yet for this quiz.<b></b></h4>
                                           </td>
                                        </tr>
                                    EOT;
                            }
                            ?>
                        </tbody>
                    </table>

                </section>
                <h4 id="todelete" style="margin: 20px; padding-left: 20px; font-weight:bold;">
                    Click the button to delete the quiz: &nbsp; &nbsp; &nbsp;
                    <form id="deletequiz" method="post" onsubmit="removequiz()" style="display:inline;">
                        <input type="hidden" name="quizid" value="<?php echo $quizid ?>">
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deletequizconfirm"><b>Delete Quiz</b></button>
                    </form>
                </h4>
                <a href="quiz.php"><button class="btn btn-info" style="margin-left:40px;"><b>Back to Quizzes</b></button></a>

            </section>
            <div style="height:50px">
            </div>
        </section>
        <!--main content end-->
    </section>

    <input type="text" id="quizlink" style="position:fixed; top:-50px;" value="https://anyquiz.me/quiztaking.php?quizcode=<?php echo $quizcode; ?>">
    <!-- pop up alert -->
    <div style="position:fixed; top:80px; width:100%; padding-left:42px; z-index:6;">
        <div id="popupalert" class="alert alert-danger" style="border-radius:6px; width:450px; margin:auto; display:none; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">
            <i class='icon_error-circle'></i>&nbsp; &nbsp; <span id="alertcontent"></span>
        </div>
        <div id="popupsuccess" class="alert alert-success" style="border-radius:6px; width:320px; margin:auto; display:none; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">
            <i class='icon_check_alt'></i>&nbsp; &nbsp; <span id="successcontent"></span>
        </div>
    </div>

    <!-- modals for confirm remove actions -->

    <div id="deletequizconfirm" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLongTitle" style="color:34aadc; font-weight:bold"><i class="icon_info"></i>&nbsp; &nbsp; Delete "<?php echo $quiztitle; ?>"?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-content" style="padding:15px 20px; box-shadow:none; -webkit-box-shadow:none;">
                <b>Are you sure you want to delete the entire quiz and all the records - settings, questions, attempt history - that are associated with it?
                    <br><br><span style="color:maroon">WARNING: This action cannot be undone!</span></b>
            </div>
            <div class="modal-footer" style="margin:0">
                <button type="button" class="btn btn-info" data-dismiss="modal"><b>Cancel</b></button>
                <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="$('#deletequiz').submit();"><b>Delete forever</b></button>
            </div>
        </div>
    </div>

</body>

</html>
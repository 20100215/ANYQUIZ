<!--Check if user is logged in-->
<?php
session_start();
if (!isset($_SESSION["username"])) {
  header("Location: login.php");
  exit();
}
unset($_SESSION["quizfetchfailed"]);
include_once("./src/checkmaintenancestatus.php"); 
include_once("./src/dbconnect.php");
$userid = $_SESSION['userid'];

if (isset($_GET['page']) && $_GET['page'] < 0) {
  $offset = 0;
  $page = 1;
} else if (isset($_GET['page']) && $_GET['page'] > 0) {
  $page = $_GET['page'];
  $offset = ($page - 1) * 20;
} else {
  $offset = 0;
  $page = 1;
}

$sql = $conn->prepare("SELECT * FROM results WHERE `from_user_id` = ? ORDER BY attempt_datetime DESC LIMIT 20 OFFSET ?");
$sql->bind_param("ss", $userid, $offset);
$sql->execute();
$result = $sql->get_result();
if (isset($_GET['page']) && $result->num_rows == 0) {
  //no results in page
  header("Location: index.php");
  exit();
}
$sql->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Home | ANYQUIZ</title>
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

  <!--custome script for all page-->
  <script src="js/scripts.js"></script>

  <!-- nice scroll -->
  <script src="js/jquery.nicescroll.js" type="text/javascript"></script>

  <script type="text/javascript" src="./src/auth.js"></script>

  <!-- bootstrap -->
  <script src="js/bootstrap.min.js"></script>
  <script>
    $(document).ready(function() {

      <?php
      if ($_SESSION["showwelcomeinfo"] == 0) {
        echo "showmodal();";
        $_SESSION["showwelcomeinfo"] = 1;
      }
      ?>

      function showmodal() {
        if (localStorageAvailable()) {
          if (localStorage.DoNotShowMessageAgain && localStorage.DoNotShowMessageAgain === "true") {
            // do nothing since user disabled the message to be shown
          } else {
            // show welcome modal
            $('#welcome').modal('show');
          }
        } else {
          // show welcome modal
          $('#welcome').modal('show');
        }
      }

      function localStorageAvailable() {
        if (typeof(Storage) !== "undefined") {
          return true;
        } else {
          return false;
        }
      }

      $('#letsgo').click(function() {
        if ($('#donotshowmessageagain').attr('checked')) {
          if (localStorageAvailable())
            localStorage.DoNotShowMessageAgain = "true";
        }
      })

      $('html').getNiceScroll().remove();
      $('html').css({
        "overflow-x": "hidden"
      });
    });
  </script>
  <style>
    /*for the intro modal*/
    div[id^="versioninfodiv"] p {
      font-size: 15px;
      margin-left: 5px;
      margin-right: 10px;
      margin-bottom: 5px;
    }

    .modal h4 {
      font-weight: bold;
      display: inline-block;
      font-size: 16px;
    }

    .vlist li {
      position: relative;
      padding: 0px !important;
    }

    .vlist {
      list-style: none;
      padding: 0;
      margin-left: 31px;
      margin-right: 10px;
      margin-bottom: 10px;
      font-size: 15px;
    }

    .vlist li:before {
      content: "\4e";
      /* FontAwesome Unicode */
      font-family: 'ElegantIcons';
      display: inline-block;
      margin-left: -1.3em;
      /* same as padding-left set on li */
      width: 1.3em;
      /* same as padding-left set on li */
    }

    .part1 b,
    .part1 li:before {
      color: #28bd41;
    }

    .part2 b,
    .part2 li:before {
      color: #3cb1c3;
    }

    td {
      padding: 7px 15px 5px !important
    }
  </style>
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
          <li class="active">
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
          <div class="col-lg-12">
            <h3 class="quizTitle" style="margin: 20px; padding-bottom: 10px; font-weight:bold;">
              <i class="icon_house"></i>&nbsp; &nbsp; &nbsp;Dashboard
            </h3>
          </div>
        </div>


        <!--Number of quizzes made and view quizzes button-->
        <div class="row">
          <h4 style="margin:0px auto 20px 50px; font-weight:bold; display:inline-block; width:310px;">
            Quizzes created: &nbsp;
            <span style="color:blue;">
              <?php
              /* get number of quizzes made by user */
              $sql = $conn->prepare("SELECT COUNT(quiz_id) FROM quizzes WHERE from_user_id = ?");
              $sql->bind_param("s", $_SESSION['userid']);
              $sql->execute();
              $result2 = $sql->get_result();
              while ($row2 = $result2->fetch_assoc()) {
                $numquizzes = $row2['COUNT(quiz_id)'];
                echo $numquizzes;
              }
              ?>
            </span>
            <a href="quiz.php"><button class="btn btn-info" style="margin-left:20px;"><b>View Quizzes</b></button></a>
          </h4>

          <?php
          /* get number of pending/ungraded response sets */
          $numessays = 0;

          $sql = $conn->prepare("SELECT quiz_id FROM quizzes WHERE from_user_id = ? AND has_essay = 1");
          $sql->bind_param("s", $_SESSION['userid']);
          $sql->execute();
          $result5 = $sql->get_result();
          $sql->close();
          while ($row5 = $result5->fetch_assoc()) {
            $currquizid = $row5['quiz_id'];
            $sql = $conn->prepare("SELECT result_id FROM results WHERE from_quiz_id = ? AND is_final = '0'");
            $sql->bind_param("s", $currquizid);
            $sql->execute();
            $result6 = $sql->get_result();
            $numessays += $result6->num_rows;
            $sql->close();
          }

          if ($numessays > 0) {
            echo <<<EOT
                <h4 style="margin:0px auto 20px 50px; font-weight:bold; color:#cca300; display:inline-block; width:380px;">
                  Pending response sets: &nbsp; <span style="color:red;">$numessays</span>
                  <a href="responselist.php"><button class="btn btn-warning" style="margin-left:20px;"><b>View and Check</b></button></a>
                </h4>
              EOT;
          }

          ?>

        </div>

        <!--Project Activity start-->
        <section class="panel" style="margin: 0px 30px 20px; max-width:1050px; border:2px solid #e6e6e6;">
          <div class="panel-body progress-panel" style="border:1px; padding:15px 15px 10px">
            <div class="row">
              <div class="col-lg-8 task-progress pull-left">
                <h1><b>Recent quiz attempts:</b></h1>
              </div>
              <div class="col-lg-4">
              </div>
            </div>
          </div>
          <table class="table table-hover personal-task" style="margin-bottom:4px;">
            <?php
            if ($result->num_rows > 0) {
              echo <<<EOT
                <thead>
                  <tr>
                    <td><b>#</b></td>
                    <td><b>Quiz attempted</b></td>
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
              $count = $offset + 1;
              $haspending = 0;
              $hasfeedbackbutton = 0;
              if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                  //get quiz name
                  $sql = $conn->prepare("SELECT `quiz_title`,`has_essay`,`is_viewquestions` FROM `quizzes` WHERE `quiz_id` = ?");
                  $sql->bind_param("s", $row['from_quiz_id']);
                  $sql->execute();
                  $result2 = $sql->get_result();
                  $sql->close();
                  $row2 = $result2->fetch_assoc();
                  $quiztitle = $row2['quiz_title'];
                  $hasessay = $row2['has_essay'];
                  $isviewquestions = $row2['is_viewquestions'];
                  $attempttime = $row['attempt_datetime'];
                  $resultid = $row['result_id'];
                  $duration = $row['duration'];
                  $score = round($row['score'], 2);
                  $perfectscore = $row['perfect_score'];
                  $isfinal = $row['is_final'];

                  //check the score for status
                  if ($score == -2) {
                    $scorecomment = "Unfinished attempt";
                    $remark = "";
                  } else if ($score == -1) {
                    $scorecomment = "Invalid attempt";
                    $remark = "";
                  } else {
                    /* check if there are pending answers */
                    if ($isfinal == 0) {
                      $scorecomment = $score . " / " . $perfectscore . " (Partial)";
                      $remark = "*";
                      $btntype = "secondary";
                      $haspending = 1;
                    } else {
                      $scorecomment = $score . " / " . $perfectscore . " (" . intval($score * 100 / $perfectscore) . "%)";
                      $remark = "";
                      $btntype = "primary";
                    }
                  }

                  //check if there is a need to display view feedback button
                  if ($hasessay == 1 && $isviewquestions == 1 && $score >= 0) {
                    $viewfeedbackbutton = <<<EOT
                        <a href="responseviewing.php?resultid=$resultid"><button type="button" class="btn btn-$btntype btn-sm" style="padding:3px 9px; width:66px; margin-top:-3px; margin-right:5px;"><b>View</b></button></a>
                      EOT;
                    $hasfeedbackbutton = 1;
                  } else {
                    $viewfeedbackbutton = "";
                  }

                  echo <<<EOT
                        <tr>
                        <td>$count$remark</td>
                        <td>$quiztitle</td>
                        <td>$attempttime</td>
                        <td>$duration</td>
                        <td>$scorecomment</td>
                        <td>$viewfeedbackbutton</td>
                        </tr>
                      EOT;
                  $count++;
                }
              }

              if ($haspending == 1 || $hasfeedbackbutton == 1) {
                echo "<tr><td colspan='4' style='font-size:11.5px;'>";
                if ($hasfeedbackbutton == 1) echo "<b>Note: Only constructed response answers and feedbacks are permanently stored and viewable by users.</b>";
                echo "</td><td colspan='2' style='font-size:11.5px;' align='right'>";
                if ($haspending == 1) echo "<b>*Some items not yet scored</b>";
                echo "</td></tr>";
              }

              if ($result->num_rows == 0) {
                echo <<<EOT
                      <tr>
                        <div style="text-align:center; padding:20px;">
                          <h4><b>No quiz attempts made yet.<br><br></b></h4>
                    EOT;


                if ($result->num_rows == 0 && $numquizzes < 1) {
                  echo <<<EOT
                      <h3 style="color:#17a2b8; margin:15px auto 10px auto; font-size:22px;"><b>New to ANYQUIZ?</b></h3>
                      <h3 style="color:#17a2b8; margin:10px auto 20px auto; font-size:22px;"><b>Start by taking the sample quiz below! (recommended)</b></h3>
                      <form id="quiz1" method="GET" action="quiztaking.php">
                        <button type="submit" class="btn btn-lg btn-info" name="quizcode" value="SAMPLE" style="margin-bottom:10px;"><b>Go to Sample Quiz</b></button>
                      </form>
                  EOT;
                }

                echo "</div></tr>";
              }
              ?>
            </tbody>
          </table>

        </section>

        <?php
        /* Pagination (show only when a result exists */
        $sql = $conn->prepare("SELECT COUNT(result_id) FROM results WHERE `from_user_id` = ?");
        $sql->bind_param("s", $userid);
        $sql->execute();
        $result = $sql->get_result();
        while ($num = $result->fetch_assoc()) {
          if ($num['COUNT(result_id)'] > 20) {
            echo <<<EOT
                <div class="row" style="padding-left: 50px; width:75%; font-size:18px;">
                <b>Go to page:</b> &nbsp;
              EOT;
            $numpages = ceil(($num['COUNT(result_id)']) / 20);
            for ($i = 1; $i <= $numpages; $i++) {
              echo <<<EOT
                <a href="index.php?page=$i"><button type="button" class="btn btn-info" style="padding:6px; width:25px;"><b>$i</b></button></a>&nbsp;&nbsp;&nbsp;
                EOT;
            }

            echo "&nbsp; <span style='font-size:14px; font-weight:bold;'>Showing result #". ($page - 1) * 20 + 1 ."-". min($page * 20,$num['COUNT(result_id)']) ." of ". $num['COUNT(result_id)'] ."</span></div>";
          }
        }
        ?>

        <h4 style="text-align:center; margin:25px 30px; max-width:1050px;"><b>Got a quiz code? Take a quiz now by clicking the button below!<br><br>
            <a href="quizfetching.php"><button type="button" class="btn btn-lg btn-success" name="quizcode" value="SAMPLE"><b>Take a Quiz</b></button></a></b></h4>









      </section>
      <div style="height:50px">
      </div>
    </section>
    <!--main content end-->
  </section>

  <!-- Welcome modal -->
  <div id="welcome" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="padding-bottom:0">
    <div class="modal-dialog modal-lg" style="padding-bottom:0">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLongTitle" style="color:#34aadc; font-weight:bold"><i class="icon_info"></i>&nbsp; &nbsp; Welcome to ANYQUIZ</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" style="margin-bottom:6px;">&times;</span>
        </button>
      </div>
      <div class="modal-content" style="padding:5px 10px; box-shadow:none; -webkit-box-shadow:none;">
        <div class="col-lg-12">
          <div style="text-align:center">
            <h3 style="font-size:25.8px; margin-bottom:20px;"><b>Get ready for a <span style="color:#e6c300">BRAND NEW</span> way of learning!</b></h3>
          </div>
          <div id="versioninfodiv4">
            <p>Here are what you can do in the site:</p>
            <ul class="vlist part1">
              <li><b>Create quizzes</b> and <b>practice</b> them by your own or <b>share</b> with others.</li>
              <li><b>Toggle and edit quiz settings</b> including time and attempt limits, question
                display and quiz monitoring modes, and accessibility of the quiz
                itself with its questions and correct answers.</li>
              <li>Create questions of <b>different types</b>: Multiple choice (single or multiple answers),
                true or false (single or multiple statements), fill in the blanks, and constructed response.</li>
              <li>Toggle <b>number of points</b> and <b>accepted answers</b> per question.</li>
            </ul>
            <ul class="vlist part2">
              <li><b>Take quizzes</b> by others anytime through their <b>links</b> and <b>access codes</b>.</li>
              <li>View <b>scores and feedback</b> immediately after a quiz taking session.</li>
              <li>View your <b>attempt log</b> and manage that of others taking your quizzes.</li>
            </ul>
            <div style="text-align:center">
              <h3 style="font-size:20px; margin-bottom:15px;"><b>ANYQUIZ: <i>About anything, for anyone.</i></b></h3>
            </div>
          </div>

        </div>
      </div>
      <div class="modal-footer" style="margin:0px; text-align:right; padding:15px;">
        <button id="letsgo" type="button" class="btn btn-primary btn-lg" data-dismiss="modal" style="margin-right:10px;"><b>LET'S GO!</b></button>
      </div>
      <div style="position:relative; bottom:50px; left:30px; width:250px;">
        <input type="checkbox" name="donotshowmessageagain" id="donotshowmessageagain" style="cursor:pointer;">
        <label for="donotshowmessageagain" style="cursor:pointer;">Do not show this message again</label>
      </div>
    </div>
  </div>

</body>

</html>
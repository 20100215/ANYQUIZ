<!--Check if user is logged in-->
<?php
session_start();
if (!isset($_SESSION["username"])) {
  header("Location: login.php");
  exit();
}

include_once("./src/checkmaintenancestatus.php");
include_once("./src/dbconnect.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Pending Responses List | ANYQUIZ</title>
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
  </script>

  <link href="css/offline-theme-chrome.css" rel="stylesheet">
  <link href="css/offline-language-english.min.css" rel="stylesheet">
  <script src="js/offline.min.js" type="text/javascript"></script>
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
          <div class="col-lg-12">
            <h3 style="margin: 20px; padding-bottom: 10px; font-weight:bold;">
              <i class="icon_document"></i>&nbsp; &nbsp; &nbsp;View pending response sets
            </h3>
          </div>
        </div>
        <div class="row">
          <h4 style="margin:0px auto 20px 50px; font-weight:bold; display:inline-block;">
            Click the Check button for each response to view its content.
          </h4>
        </div>

        <!--Project Activity start-->
        <section class="panel" style="margin-left:30px; margin-right:30px; max-width:900px; border:2px solid #e6e6e6;">

          <table class="table table-hover personal-task" style="margin-bottom:4px;">
            <?php

            $responselist = [];
            $ndx = 0;
            /* list quizzes where there are essay questions
               then for every quiz, list all attempts,
               then for every attempt, check if there are ungraded essays (score = -1) */

            /* store response set to array, then sort using descending order of datetime */
            $sql = $conn->prepare("SELECT quiz_id, quiz_title, access_code FROM quizzes WHERE from_user_id = ? AND has_essay = 1");
            $sql->bind_param("s", $_SESSION['userid']);
            $sql->execute();
            $result2 = $sql->get_result();
            while ($row2 = $result2->fetch_assoc()) {
              $currquizid = $row2['quiz_id'];
              $currquiztitle = $row2['quiz_title'];
              $curraccesscode = $row2['access_code'];
              $sql = $conn->prepare("SELECT * FROM results WHERE from_quiz_id = ? AND is_final = '0'");
              $sql->bind_param("s", $currquizid);
              $sql->execute();
              $result3 = $sql->get_result();
              while ($row3 = $result3->fetch_assoc()) {
                $currresultid = $row3['result_id'];
                $currfromuserid = $row3['from_user_id'];
                $currdatetime = $row3['attempt_datetime'];
                $currscore = $row3['score'];
                $currperfectscore = $row3['perfect_score'];
                $currisfinal = $row3['is_final'];
                if ($currfromuserid == $_SESSION['userid']) {
                  $currusername = "Me";
                } else {
                  $sql = $conn->prepare("SELECT username FROM users WHERE `user_id` = ?");
                  $sql->bind_param("s", $currfromuserid);
                  $sql->execute();
                  $result4 = $sql->get_result();
                  while ($row4 = $result4->fetch_assoc()) {
                    $currusername = $row4['username'];
                  }
                }
                //store table row data to array for sorting
                $responselist[$ndx] = array(
                  'quiztitle' => $currquiztitle,
                  'quizid' => $currquizid,
                  'quizcode' => $curraccesscode,
                  'attemptedby' => $currusername,
                  'attempttime' => $currdatetime,
                  'partialscore' => round($currscore, 1) . " / " . $currperfectscore,
                  'resultid' => $currresultid
                );
                $ndx++;
              }
            }

            if ($ndx > 0) {
              /* sort array by datetime first */
              function date_compare($a, $b)
              {
                $t1 = strtotime($a['attempttime']);
                $t2 = strtotime($b['attempttime']);
                return $t2 - $t1;
              }
              usort($responselist, 'date_compare');
              /* proceed to listing */
              echo <<<EOT
                                    <thead>
                                    <tr>
                                        <td><b>#</b></td>
                                        <td><b>Quiz Title</b></td>
                                        <td><b>Attempted by</b></td>
                                        <td><b>Attempt time</b></td>
                                        <td><b>Partial score</b></td>
                                        <td></td>
                                        </tr>
                                    </thead>
                                EOT;
            }

            ?>


            <tbody>
              <?php
              $count = 1;
              if ($ndx > 0) {
                for ($x = 0; $x < $ndx; $x++) {
                  /* extract data from array to display */
                  $currquiztitle = $responselist[$x]['quiztitle'];
                  $currquizid = $responselist[$x]['quizid'];
                  $curraccesscode = $responselist[$x]['quizcode'];
                  $currusername = $responselist[$x]['attemptedby'];
                  $currdatetime = $responselist[$x]['attempttime'];
                  $currpartialscore = $responselist[$x]['partialscore'];
                  $resultid = $responselist[$x]['resultid'];
                  echo <<<EOT
                                            <tr>
                                            <td>$count</td>
                                            <td>$currquiztitle</td>
                                            <td>$currusername</td>
                                            <td>$currdatetime</td>
                                            <td>$currpartialscore</td>
                                            <td>
                                                <form id="checkresponse$count" method="GET" action="responsechecking.php">
                                                    <input type="hidden" name="resultid" value="$resultid">
                                                    <input type="hidden" name="uname" value="$currusername">
                                                </form>
                                                <form id="deleteattempt$count" method="get" onsubmit="deleteattempt2(event,$count)" style="display:inline;">
                                                    <input type="hidden" name="quiztitle" value="$currquiztitle">
                                                    <input type="hidden" name="quizid" value="$currquizid">
                                                    <input type="hidden" name="quizcode" value="$curraccesscode">
                                                    <input type="hidden" name="resultid" value="$resultid">
                                                    <button type="button" onclick="document.getElementById('checkresponse$count').submit()" class="btn btn-warning btn-sm" style="padding:3px 9px; width:66px; margin-top:-3px; margin-right:5px;"><b>Check</b></button>
                                                    <button type="submit" class="btn btn-danger btn-sm" style="padding:3px 9px; width:66px; margin-top:-3px;"><b>Remove</b></button>
                                                </form>
                                            </td>
                                            </tr>
                          EOT;
                  $count++;
                }
              } else {
                echo <<<EOT
                              <tr style="padding-bottom:10px;">
                                 <td colspan="6" style="text-align:center;" class="text-info">
                                     <img src="./img/noessay.png">
                                     <h4><b>Nothing to check for now. It's break time!<b></b></h4>
                                 </td>
                              </tr>
                        EOT;
              }
              ?>
            </tbody>
          </table>

        </section>
        <p style="margin:0px 30px 10px 40px; max-width:900px;">
          <b>Tip 1: </b>To stop receiving new or extra response sets from a quiz that is no longer active, de-activate
          the quiz either by changing its visibility to "Private" or by limiting the number of attempts
          in the "Edit Quiz Settings" page.
        </p>
        <p style="margin:0px 30px 10px 40px; max-width:900px;">
          <b>Tip 2: </b>To recheck a response set, simply access the source quiz from the quizzes tab and click the
          "Recheck" button at the right side of the list item.
        </p>
        <a href="index.php"><button class="btn btn-info" style="margin-left:40px;"><b>Back to Dashboard</b></button></a>
        &nbsp; &nbsp; &nbsp;
        <a href="quiz.php"><button class="btn btn-info"><b>View Quizzes</b></button></a>

      </section>
      <div style="height:50px">
      </div>
    </section>
    <!--main content end-->
  </section>

</body>

</html>
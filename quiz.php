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

$sql = $conn->prepare("SELECT * FROM quizzes WHERE `from_user_id` = ?");
$sql->bind_param("s", $userid);
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

  <script>
    $(document).ready(function() {

      $(".panel-default").hover(
        function() {
          $(this).addClass('shadow-lg').css('cursor', 'pointer');
        },
        function() {
          $(this).removeClass('shadow-lg');
        }
      );

      $('html').getNiceScroll().remove();
      $('html').css({
        "overflow-x": "hidden"
      });

    });
  </script>

  <script type="text/javascript" src="./src/auth.js"></script>
  <style>
    .panel-default:hover {
      box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
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
            <h3 class="quizTitle" style="margin: 20px; padding-bottom: 10px; font-weight:bold;">
              <i class="icon_document"></i>&nbsp; &nbsp; &nbsp;My Quizzes
            </h3>
          </div>
          <div class="col-lg-12">
            <?php
            if ($result->num_rows > 0) {
              echo <<<EOT
                      <h4 id="instructions" style="margin: 20px; margin-bottom: 5px; padding-left: 20px; font-weight:bold;">
                          Click on any of the panels below to view actions and attempt results gathered for the quiz.<br><br>
                          To create a new quiz, click the Create New Quiz button on top.
                      </h4>
                  EOT;
            }
            ?>
          </div>
        </div>



        <?php
        $count = 1;
        $hasessay = 0;
        if ($result->num_rows > 0) {
          echo "<div class='row hasquiz' style='margin-left:15px; margin-right:15px; font-weight:bold;'>";
          while ($row = $result->fetch_assoc()) {
            /* get quiz id, title, code */
            $quiztitle = $row['quiz_title'];
            $quizid = $row['quiz_id'];
            $quizcode = $row['access_code'];
            if($row['has_essay'] == 1){
              $hasessay = 1;
            }

            /* get number of questions */
            $sql = $conn->prepare("SELECT COUNT(question_id) FROM questions WHERE from_quiz_id = ?");
            $sql->bind_param("s", $quizid);
            $sql->execute();
            $result2 = $sql->get_result();
            $sql->close();
            while ($row2 = $result2->fetch_assoc()) {
              $numquestions = $row2['COUNT(question_id)'];
            }

            /* get number of points*/
            $sql = $conn->prepare("SELECT SUM(points) FROM questions WHERE from_quiz_id = ?");
            $sql->bind_param("s", $quizid);
            $sql->execute();
            $result2 = $sql->get_result();
            $sql->close();
            while ($row2 = $result2->fetch_assoc()) {
              $numpoints = $row2['SUM(points)'];
            }

            echo <<<EOT
                <form id="quiz$count" method="GET" action="quizinfo.php" style="display:inline;">
                  <input type="hidden" name="quiztitle" value="$quiztitle">
                  <input type="hidden" name="quizid" value="$quizid">
                  <input type="hidden" name="quizcode" value="$quizcode">
                  <div class="panel panel-default hover" onclick="document.getElementById('quiz$count').submit();" style="margin:20px; width:200px; display:block; float:left;">
                    <img src="./img/quizpanel.png">
                    <div class="panel-body" style="height:70px;">$quiztitle</div>
                    <div class="panel-footer">$numquestions questions | $numpoints points</div>
                  </div>
                </form>
            EOT;

            $count++;
          }
          echo "</div>";
        } else {
          echo <<<EOT
                  <div class="row noquiz" style="margin-left:30px; margin-right:30px; text-align:center;">

                    <img src="./img/noquiz.png">
                    <h2><b>No quizzes created yet!</b></h1>
                    <h4><b>Click the button below to create your first quiz</b></h3><br>
                      <a href="quiz_maker.php">
                        <button class="btn btn-info btn-lg" style="font-size:18px;"><span class="fa fa-plus">&nbsp; &nbsp; </span><b>Create your first quiz</b></button>
                      </a>
      
                  </div>
                EOT;
        }
        ?>
      </section>
      <a href="index.php"><button class="btn btn-info" style="margin-left:40px;"><b>Back to Home</b></button></a>
      <?php
      if ($hasessay == 1){
        echo <<<EOT
          &nbsp; &nbsp; &nbsp;
          <a href="responselist.php"><button class="btn btn-warning"><b>View Pending Responses</b></button></a>
        EOT;
      }
      ?>
      <div style="height:50px">
      </div>
    </section>
    <!--main content end-->
  </section>
  <!-- container section start -->

</body>

</html>
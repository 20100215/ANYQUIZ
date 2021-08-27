<!--Check if user is logged in-->
<?php
session_start();
if (!isset($_SESSION["username"])) {
  header("Location: login.php");
  exit();
}
unset($_SESSION["quizfetchfailed"]);
if (!isset($_SESSION["insertquizstatus"])) {
  header("Location: index.php");
  exit();
}
unset($_SESSION['insertquizstatus']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link href="./css/bootstrap.min.css" rel="stylesheet">
  <link href="./css/bootstrap-theme.css" rel="stylesheet">
  <link href="./css/elegant-icons-style.css" rel="stylesheet" />
  <link href="./css/font-awesome.min.css" rel="stylesheet" />
  <link href="./css/jquery-jvectormap-1.2.2.css" rel="stylesheet">
  <link rel="stylesheet" href="./css/fullcalendar.css">
  <link href="./css/widgets.css" rel="stylesheet">
  <link href="./css/style.css" rel="stylesheet">
  <link href="./css/style-responsive.css" rel="stylesheet" />
  <link href="./css/xcharts.min.css" rel=" stylesheet">
  <link href="./css/jquery-ui-1.10.4.min.css" rel="stylesheet">
  <link href="./css/helptip.css" rel="stylesheet">
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
    $('document').ready(function() {
      $('html').getNiceScroll().remove();
      $('html').css({
        "overflow-x": "hidden"
      });
    });
  </script>

  <link href="css/offline-theme-chrome.css" rel="stylesheet">
  <link href="css/offline-language-english.min.css" rel="stylesheet">
  <script src="js/offline.min.js" type="text/javascript"></script>

  <title>Quiz Maker | ANYQUIZ</title>
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
          <li class="active mobile">
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

        <!--message start-->
        <div class="row" style="margin-top:16px;">
          <div class="container" style="color:forestgreen; text-align:center; font-size:18px;">
            <img style="width:220px;" src="img/success.png">
            <h2 class="quizTitle" style="margin: 20px; padding-bottom: 10px; font-weight:bold;">Successfully published quiz!</h2>
            <h4 style="font-weight:bold;">You may now go back to the home page or quizzes page to view your recently created quiz.</h4>
            <br>
            <a href="index.php"><button class="btn btn-info" style="font-size:18px;"><b>Back to Home</b></button></a>
            &nbsp; &nbsp; &nbsp;
            <a href="quiz.php"><button class="btn btn-info" style="font-size:18px;"><b>Go to Quizzes</b></button></a>
          </div>
        </div>

      </section>
      <div style="height:10px">
      </div>
    </section>
  </section>

</body>

</html>
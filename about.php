<!--Check if user is logged in-->
<?php
session_start();
if (!isset($_SESSION["username"])) {
  header("Location: login.php");
  exit();
}
unset($_SESSION["quizfetchfailed"]);

include_once("./src/checkmaintenancestatus.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>

  <title>About | ANYQUIZ</title>
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

  <script type="text/javascript" src="./src/auth.js"></script>
  <style>
    h3,
    h4,
    h5 {
      font-weight: bold;
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
          <li class="active">
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
          <div class="col-sm-5">
            <h3 style="margin: 20px 20px 10px 20px; font-weight:bold;"><i class="icon_info"></i>&nbsp; &nbsp; About and Feedback</h3>
          </div>
          <div class="col-sm-6">
            <a href="versionhistory.php"><button class="btn btn-info" style="float:right; margin: 20px 15px 0px;"><b>Show version history</b></button></a>
          </div>
        </div>
        <!-- page start-->
        <div class="row">
          <div class="col-sm-5" style="padding-left:30px; padding-right:30px;">
            <div class="recent">
              <h3><b>Send us your feedback!</b></h3>
            </div>
            <form onsubmit="contactformsubmit(event)" method="post" role="form" id="contactForm" autocomplete="off">
              <div class="form-group">
                <input name="name" class="form-control" id="name" placeholder="Your Name" data-rule="minlen:4" data-msg="Please enter at least 4 chars" />
                <div class="validation"></div>
              </div>
              <div class="form-group">
                <input class="form-control" name="email" id="email" placeholder="Your Email" data-rule="email" data-msg="Please enter a valid email" />
                <div class="validation"></div>
              </div>
              <div class="form-group">
                <input class="form-control" name="subject" id="subject" placeholder="Subject" data-rule="minlen:4" data-msg="Please enter at least 8 chars of subject" />
                <div class="validation"></div>
              </div>
              <div class="form-group">
                <textarea class="form-control" name="message" rows="5" data-rule="required" data-msg="Please write something for us" placeholder="Message" style="resize: none;"></textarea>
                <div class="validation"></div>
              </div>

              <div class="text-center"><button type="submit" class="btn btn-primary btn-lg submit"><b>Send Message</b></button></div>
            </form>
            <br>
            <div id="alertsuccess" class="alert alert-success" style="display:none;"><i class="icon_check_alt"></i>&nbsp; &nbsp;Your message has been sent. Thank you!</div>
            <div id="alertfailed" class="alert alert-danger" style="display:none;"></div>
          </div>

          <div class="col-sm-6" style="padding-left:30px; padding-right:30px;">
            <div class="recent">
              <h3>About</h3>
            </div>
            <div class="">
              <p style="text-align:justify;">
                ANYQUIZ is a free-for-all, fully-featured site where one can create, share, and take any
                kind of quiz coming from you yourself or your friends and teachers. This app was originally
                designed as a project that primarily aims to create a more efficient learning strategy for
                all of us as we combat with the pandemic that already compromised the quality of education
                we are experiencing. Several settings can be set to align with the desired atmosphere
                from relaxing and casual to real exam simulations with rigorous monitoring of user events.
              </p>

              <h4>Contact information:</h4>
              Address: University of San Carlos - Talamban Campus, Cebu City 6000<br>
              Phone: +63 998 490 1696 &nbsp; &nbsp; Email: wdayata@gmail.com<br>
              <h4>Team Leader, Back-end Developer</h4>Wayne Matthew Dayata
              <h4>Design Leads, Front-end Developers</h4>Mary Angela Retuya and Antoine Federico Godinez<br>
              <h4>Special Thanks to</h4>
              Mr. Chris Ray Belarmino, Mr. Khent Dela Paz, and Mr. Patrick Troy Elalto<br>USC-TC Web Development instructors<br><br>
              <div class="row">
                <div class="col-sm-6">
                  <h5><b>Current version: 2.1.4 (August 14, 2021)</b></h5>
                  <h5><b>&copy; ANYQUIZ 2021</b></h5>
                </div>
                <div class="col-sm-6">
                  <a href="versionhistory.php"><button class="btn btn-info" style="float:right; margin: 20px 15px 0px;"><b>Show version history</b></button></a>
                </div>
              </div>
            </div>
          </div>
      </section>
      <div style="height:10px">
      </div>
    </section>
  </section>

  <!--main content end-->
  </section>
  <!-- container section start -->

</body>

</html>
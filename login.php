<!--Check if user is logged in-->
<?php
session_start();
if (isset($_SESSION["username"])) {
  header("Location: index.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <title>Login | ANYQUIZ</title>

  <link rel="icon" href="favicon.ico">
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

  <link rel="icon" href="favicon.ico">
  <meta name="description" content="ANYQUIZ is a fully featured quiz maker for school, business, or just for fun. Create one now and explore its awesome features that match your desire and purpose.">
  <meta name="author" content="Wayne Dayata">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="css/offline-theme-chrome.css" rel="stylesheet">
  <link href="css/offline-language-english.min.css" rel="stylesheet">
  <script src="js/offline.min.js" type="text/javascript"></script>
  <script src="src/auth.js" type="text/javascript"></script>

</head>

<body class="login-img3-body">

  <div class="container">

    <div class="login-form" style="border-radius:10px; padding:23px 15px 0px; margin-top:7%;">
      <div class="login-wrap">
        <p class="login-img" style="margin:-5px;"><img style="height:92px" src="./img/anyquiz.png"></i></p>
        <p class="anyquizTitle" style="text-align: center; color:black; font-weight:bold; font-size:24px;">ANYQUIZ</p>

        <!-- sign-in form -->
        <form id="signinform" method="get" onsubmit="signinsubmit(event)">
          <div class="input-group">
            <span class="input-group-addon"><i class="icon_profile"></i></span>
            <input name="loginUser" class="form-control login" placeholder="Username" maxlength="20" autofocus>
          </div>
          <div class="input-group">
            <span class="input-group-addon"><i class="icon_key"></i></span>
            <input type="password" name="loginPassword" class="form-control login" placeholder="Password">
          </div>
          <label class="checkbox" style="display:inline-block; width:150px; margin-top:4px;">
            <input type="checkbox" name="isrememberme" value="remember-me"> Remember me
          </label>
          <span style="color:rgb(1,139,209); cursor:pointer; float:right; margin-top:4px;" data-toggle="modal" data-target="#forgetpasswordmsg"> Forgot Password?</span>
          <button class="btn btn-primary btn-lg btn-block" type="submit"><b>Log in</b></button>
          <a href="signup.php"><button class="btn btn-info btn-lg btn-block" type="button"><b>Create new account</b></button></a>
        </form>
        <br><span style="display:block; text-align:center; font-size:12px; font-weight:bold; color:grey;">&copy; &nbsp; ANYQUIZ 2021 &nbsp; &nbsp; | &nbsp; &nbsp; v2.1.4 (08/18/2021)</span>
      </div>
    </div>

  </div>

  <!-- pop up alert when attempting to click other links during a quiz attempt -->
  <div style="position:fixed; top:30px; width:100%">
    <div id="popupalert" class="alert alert-danger" style="border-radius:6px; width:400px; margin:auto; display:none; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">
      <i class='icon_error-circle'></i>&nbsp; &nbsp; <span id="alertcontent"></span>
    </div>
  </div>

  <!-- modal for forget password -->
  <div id="forgetpasswordmsg" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-header">
        <h4 class="modal-title" id="exampleModalLongTitle" style="color:#34aadc; font-weight:bold"><i class="icon_info"></i>&nbsp; &nbsp; Forgot password?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" style="margin-bottom:6px;">&times;</span>
        </button>
      </div>
      <div class="modal-content" style="padding:5px 10px; box-shadow:none; -webkit-box-shadow:none;">
        <div class="col-lg-12">
          <div class="recent">
            <p style="margin-top:20px; text-align:justify"><b>
              Enter your username and the associated email you used to create your account. Then, in the textarea at the bottom,
              provide ample information about your recent activity such as the quizzes you have made, taken, and attempted. This
              is for us to evaluate and verify your identity before we send a link to you via email to reconfigure your account.
            </b></p>
          </div>
          <form onsubmit="contactformsubmit(event)" method="post" role="form" id="contactForm">
            <div class="form-group">
              <input name="name" class="form-control" id="name" placeholder="Username" data-rule="minlen:4" data-msg="Please enter at least 4 chars" />
              <div class="validation"></div>
            </div>
            <div class="form-group">
              <input class="form-control" name="email" id="email" placeholder="Email" data-rule="email" data-msg="Please enter a valid email" />
              <div class="validation"></div>
            </div>
            <div class="form-group">
              <textarea class="form-control" name="message" rows="4" data-rule="required" data-msg="Briefly describe your recent activity here" placeholder="Briefly describe your recent activity here" style="resize: none;"></textarea>
              <div class="validation"></div>
            </div>
            <input type="hidden" name="subject" value="FORGET PASSWORD">

            <div class="text-center"><button type="submit" class="btn btn-primary submit"><b>Send Message</b></button></div>
          </form>
          <br>
          <div id="alertsuccess" class="alert alert-success" style="display:none;"><i class="icon_check_alt"></i>&nbsp; &nbsp;Your message has been submitted. Please allow up to a week for us to process your request. Thank you!</div>
          <div id="alertfailed" class="alert alert-danger" style="display:none;"></div>
        </div>
      </div>
      <div class="modal-footer" style="margin:0">
        <button type="button" class="btn btn-primary" data-dismiss="modal"><b>Close</b></button>
      </div>
    </div>
  </div>


</body>

</html>
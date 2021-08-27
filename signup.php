<!--Check if user is logged in-->
<?php
  session_start();
  if (isset($_SESSION["username"])) {
    header("Location: index.php");
    exit();
  }
  
include_once("./src/checkmaintenancestatus.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <title>Create New Account | ANYQUIZ</title>

  <link rel="icon" href="favicon.ico">
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/bootstrap-theme.css" rel="stylesheet">
  <link href="css/elegant-icons-style.css" rel="stylesheet" />
  <link href="css/font-awesome.css" rel="stylesheet" />
  <link href="css/style.css" rel="stylesheet">
  <link href="css/style-responsive.css" rel="stylesheet" />
  <link rel="icon" href="favicon.ico">
  <meta name="description" content="ANYQUIZ is a fully featured quiz maker for school, business, or just for fun. Create one now and explore its awesome features that match your desire and purpose.">
  <meta name="author" content="Wayne Dayata">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <script src="js/jquery.js"></script>
  <script src="js/jquery-ui-1.10.4.min.js"></script>
  <script src="js/jquery-1.8.3.min.js"></script>
  
  <script src="js/bootstrap.min.js"></script>
  
  <script type="text/javascript" src="./src/auth.js"></script>
  <script type="text/javascript" src="js/jquery-ui-1.9.2.custom.min.js"></script>

  <link href="css/offline-theme-chrome.css"  rel="stylesheet">
  <link href="css/offline-language-english.min.css"  rel="stylesheet">
  <script src="js/offline.min.js" type="text/javascript"></script>

</head>
<body class="login-img3-body">

  <div class="container">

    <div class="login-form" style="border-radius:10px; padding:24px 15px 14px 15px; margin-top:7%;">
      <div class="login-wrap">
        
        
        <p class="anyquizTitle" style="text-align: center; color:black; font-weight:bold; font-size:18px;">
            Create an account to get started!
        </p>

        <!-- sign-in form -->
        <form id="signupform" method="get" onsubmit="signupsubmit(event)" autocomplete="off">
          <div class="input-group">
            <span class="input-group-addon"><i class="icon_profile"></i></span>
            <input name="username" class="form-control login" placeholder="Username" maxlength="20" autofocus>
          </div>
          <div class="input-group">
            <span class="input-group-addon"><i class="icon_mail"></i></span>
            <input name="email" class="form-control login" placeholder="Email">
          </div>
          <div class="input-group">
            <span class="input-group-addon"><i class="icon_key"></i></span>
            <input type="password" name="password" class="form-control login" placeholder="Password">
          </div>
          <div class="input-group">
            <span class="input-group-addon"><i class="icon_key"></i></span>
            <input type="password" name="confirmpassword" class="form-control login" placeholder="Confirm Password">
          </div>
          <label class="checkbox">
            <input type="checkbox" name="agree" value="agree"> I agree to the 
            <a href="termsandconditions.html" target="_blank"><u>terms and conditions</u></a>
          </label>
          <button class="btn btn-info btn-lg btn-block" type="submit"><b>Sign up</b></button>
          <br><p style="color:black;"><b>Already have an account? <a href="login.php" style="color:blue;">Log in</a></b></p>
        </form>
      </div>
    </div>
    
  </div>

  <!-- pop up alert when attempting to click other links during a quiz attempt -->
  <div style="position:fixed; top:30px; width:100%">
        <div id="popupalert" class="alert alert-danger" style="border-radius:6px; width:400px; margin:auto; display:none; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">
            <i class='icon_error-circle'></i>&nbsp; &nbsp; <span id="alertcontent"></span>
        </div>
  </div>

  <!-- modals -->
  <div id="signupsuccess" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
          <div class="modal-header">
              <h4 class="modal-title" id="exampleModalLongTitle" style="color:#34aadc; font-weight:bold"><i class="icon_info"></i>&nbsp; &nbsp; Account created successfully!</h4>
          </div>
          <div class="modal-content" style="padding:15px 20px; text-align:center; line-height:1.8; box-shadow:none; -webkit-box-shadow:none;">                
            <b>Thank you for joining us in ANYQUIZ; we're really excited to see you!<br>You may now click the button below to log in to your newly created account.</b>
          </div>
          <div class="modal-footer" style="margin:0">
              <button onclick="location.replace('login.php')" type="button" class="btn btn-primary" data-dismiss="modal"><b>Log in</b></button>
          </div>
      </div>
  </div>


</body>

</html>
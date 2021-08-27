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
$sql = $conn->prepare("SELECT email FROM users WHERE user_id = ?");
$sql->bind_param("s", $_SESSION['userid']);
$sql->execute();
$result = $sql->get_result();
$sql->close();
while ($row = $result->fetch_assoc()) {
    $email = $row['email'];
}
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

        /* toggling element usability in profile page */
        function togglechangepassword() {
            if ($('input#changepassword').is(':checked')) {
                $('input[id="password"]').prop('disabled', false);
                $('input[id="confirmpassword"]').prop('disabled', false);
                $('#passwordsection').show(300);
            } else {
                $('input[id="password"]').prop('disabled', true);
                $('input[id="confirmpassword"]').prop('disabled', true);
                $('input[id="password"]').val("");
                $('input[id="confirmpassword"]').val("");
                $('#passwordsection').hide(300);
            }
        }
    </script>
    <script type="text/javascript" src="./src/auth.js"></script>
    <title>Profile | ANYQUIZ</title>
    <link rel="icon" href="favicon.ico">
    <meta name="description" content="ANYQUIZ is a fully featured quiz maker for school, business, or just for fun. Create one now and explore its awesome features that match your desire and purpose.">
    <meta name="author" content="Wayne Dayata">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
                    <li class="active mobile">
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

                        <h3 class="quizTitle" style="margin: 20px; padding-bottom: 10px; font-weight:bold;"><i class="icon_profile"></i>&nbsp; &nbsp; &nbsp;My Profile</h3>
                    </div>
                </div>

                <form id="updateaccount" method="POST" onsubmit="updateAccount(event)">

                    <!-- profile tab -->
                    <div class="row maintab">
                        <input type="hidden" name="userid" value="<?php echo $_SESSION['userid']; ?>">
                        <div class="count-4" style="padding-left: 60px; font-size: 15px; padding-bottom:20px; display:none;">
                            <!-- image upload support is not yet available -->
                            <span class="profile-ava">
                                <img alt="" style="width:100px; height:100px;" src="img/id-pic1.png">
                            </span>
                        </div>

                        <div id="accountsection" style="padding-left: 50px; font-size: 18px;">
                            <b>Update account information:</b><br>
                            <div style="padding-left:20px; padding-top:15px;">
                                Username: &nbsp; <input name="username" class="form-control" maxlength="20" style="display:inline-block; width:240px; position:relative; left:59px;" value="<?php echo $_SESSION['username']; ?>"><br>
                                Email: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <input name="email" class="form-control" style="display:inline-block; width:240px; position:relative; left:62px;" value="<?php echo $email; ?>"><br>
                                Current password: <input name="oldpassword" type="password" class="form-control login" style="display:inline-block; width:240px; position:relative; left:5px; background-color:white;border: 1px solid #b6b6b6;color:grey;"><br><br>

                                <input type="checkbox" name="changepassword" id="changepassword" onclick="togglechangepassword()"><label for="changepassword">&nbsp; Change password</label>
                                <div id="passwordsection" style="display:none; padding-left:15px;">
                                    New password: <input name="password" type="password" class="form-control login" style="display:inline; width:200px; position:relative; left:69px; background-color:white;border: 1px solid #b6b6b6;color:grey;"> <br>
                                    Confirm new password: <input name="confirmpassword" type="password" class="form-control login" style="display:inline; width:200px; position:relative; left:5px; background-color:white;border: 1px solid #b6b6b6;color:grey;"> <br>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6" style="padding-top: 19px; padding-left: 60px; font-size:13px; font-weight:bold;">
                            Click <span style="color:blue;"> Save and Update</span> to have the changes configured.
                        </div>


                        <div class="col-lg-5" style="padding-left: 60px;">
                            <button type="submit" id="saveandupdate" class="btn btn-success" style="text-align: center;width: 200px; height: 40px; border: 1px solid white; right: 150px; margin-top:15px; 
                            font-size: 15px;">
                                <b>Save and Update</b>
                            </button>
                        </div>

                        <!-- end of profile tab-->
                    </div>

                </form>


            </section>
            <a href="index.php"><button type="button" class="btn btn-info" style="margin-left:40px;"><b>Back to Home</b></button></a>
            &nbsp; &nbsp; &nbsp;
            <form id="quiz" method="GET" action="quiztaking.php" style="display:inline;">
                <button type="submit" name="quizcode" value="SAMPLE" class="btn btn-info"><b>Relaunch Sample Quiz</b></button>
            </form>
            <div style="height:50px">
            </div>
        </section>
    </section>

    <!-- pop up alert -->
    <div style="position:fixed; top:80px; padding-left:42px; width:100%">
        <div id="popupalert" class="alert alert-danger" style="border-radius:6px; width:460px; margin:auto; display:none; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">
            <i class='icon_error-circle'></i>&nbsp; &nbsp; <span id="alertcontent"></span>
        </div>
        <div id="popupsuccess" class="alert alert-success" style="border-radius:6px; width:440px; margin:auto; display:none; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">
            <i class='icon_check_alt'></i>&nbsp; &nbsp; <span id="successcontent"></span>
        </div>
    </div>

</body>

</html>
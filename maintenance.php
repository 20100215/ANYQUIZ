<?php
session_start();
session_destroy();

/*-------------------------------------------------
               ADJUST CONTENT HERE!!!!           */

$endtime = "July 5 4:00pm PST";
$message = <<<EOT
                <h4>The enhanced ANYQUIZ web app is being prepared for a whole new level of quizzing experience!</h4>
                <div id="versioninfodiv4">
                    <p>Features in the original release (March 17 - June 4, 2021):</p>
                    <ul class="vlist">
                        <li>Creating and sharing of quizzes via generated quiz code</li>
                        <li>Question types: Multiple choice (single answer) and fill in the blanks</li>
                        <li>Quiz settings: Time limit, backtracking restrictions, page focus monitoring, and quiz visibility</li>
                        <li>Post-quiz feedback and result log of recently attempted quizzes</li>
                    </ul>
                    <p>Additions and optimizations in the enhanced version:</p>
                    <ul class="vlist">
                        <li>3 new question types (5 total): True or false, multiple choice (multiple answers), and essay/constructed response</li>
                        <li>5 new quiz settings (9 total): Shuffle question order, attempt restrictions, mode of question display, visibility of correct answers, and full screen monitoring</li>
                        <li>3 new other features: Quiz settings editing, essay response checking, and overall quiz analytics</li>
                        <li>More secure and user-friendly quiz making, taking, and management environments</li>
                        <li>Several bug fixes and performance improvements made</li>
                    </ul>
                    <p style="margin-left:-1px;"><b>A million thanks to all the beta testers and to everyone who supported and contributed throughout 
                    the further development of this app. May this project be of help to all of us as continuing students in spite of this pandemic!</b></p>
                </div>
            EOT;


/* For $message - follow the version history format
-------------------------------------------------*/

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Maintenance Break</title>

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
    <style>
        div[id^="versioninfodiv"] p,
        ul {
            font-size: 13px;
        }

        div[id^="versioninfodiv"] p {
            margin-left: 10px;
            margin-bottom: 5px;
        }

        ul.vlist {
            margin-left: 40px;
            margin-bottom: 10px;
        }

        ul.vlist2 {
            margin-left: 20px;
            margin-bottom: 5px;
        }

        h4 {
            margin-top: 20px;
            margin-bottom: 15px;
            font-weight: bold;
            display: inline-block;
            font-size: 15px;
        }

        .vlist li {
            position: relative;
            list-style: square !important;
            padding: 0px !important;
            margin: 0px !important;
        }

        @media screen and (min-width: 968px) {
            .modal-dialog {
                width: 800px;
            }
        }
    </style>

    <link href="css/offline-theme-chrome.css" rel="stylesheet">
    <link href="css/offline-language-english.min.css" rel="stylesheet">
    <script src="js/offline.min.js" type="text/javascript"></script>

</head>

<body class="login-img3-body">

    <div class="container">

        <div class="login-form" style="border-radius:10px; padding:15px 14px; margin-top:7%;">
            <div class="login-wrap">
                <p class="login-img" style="margin:0"><img style="height:130px" src="./img/maintenance-icon.png"></i></p>
                <p class="anyquizTitle" style="text-align: center; color:black; font-weight:1000; font-size:32px; margin-bottom:0">Maintenance Break</p>
                <p style="color:black; font-size:15px; font-weight:300; text-align:justify">
                    We are currently working on updates to our
                    sites to bring you an even better experience in creating, taking, and managing ANYQUIZ.
                </p>
                <p style="color:black; font-size:15px; font-weight:300; text-align:justify">
                    Our pages and services are expected to be back at around <span style="font-weight:bold"><?php echo $endtime ?></span>.
                </p>
                <p style="color:black; font-size:15px; font-weight:300; text-align:justify;">
                    Thank you for your understanding. We hope to see you then!
                </p>
                <br>
                <p style="color:black; font-size:15px; margin:0"><b>Wanna know what's coming? <span style="color:rgb(1,139,209); cursor:pointer;" data-toggle="modal" data-target="#whatsnew">Click here!</span></b></p>
            </div>
        </div>
    </div>

    <!-- modal for the changes -->
    <div id="whatsnew" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLongTitle" style="color:#34aadc; font-weight:bold"><i class="icon_info"></i>&nbsp; &nbsp; What's coming?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="margin-bottom:6px;">&times;</span>
                </button>
            </div>
            <div class="modal-content" style="padding:5px 10px; box-shadow:none; -webkit-box-shadow:none;">
                <div class="col-lg-12">
                    <?php echo $message; ?>
                </div>
            </div>
            <div class="modal-footer" style="margin:0">
                <button type="button" class="btn btn-primary" data-dismiss="modal"><b>Close</b></button>
            </div>
        </div>
    </div>


</body>

</html>
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

    <link href="css/offline-theme-chrome.css" rel="stylesheet">
    <link href="css/offline-language-english.min.css" rel="stylesheet">
    <script src="js/offline.min.js" type="text/javascript"></script>

    <script type="text/javascript" src="./src/auth.js"></script>
    <script type="text/javascript" src="./src/quizmaker.js"></script>

    <script>
        $('document').ready(function() {
            $('html').getNiceScroll().remove();
            $('html').css({
                "overflow-x": "hidden"
            });
        });
    </script>
    
    <title>Create New Quiz | ANYQUIZ</title>
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

                <!--overview start-->
                <div class="row">
                    <div class="col-lg-12">
                        <h3 class="quizTitle" style="margin: 20px; padding-bottom: 10px; font-weight:bold;">Create a Quiz</h3>
                    </div>
                </div>

                <form id="quiz" method="POST" onsubmit="submitquiz(event)" autocomplete="off">

                    <!-- quiz settings tab -->
                    <div class="row maintab">
                        <div class="count-4" style="padding-left: 50px; font-size: 15px;">Enter Quiz Title: <input name="quizname" id="quizname" class="form-control" maxlength="64" placeholder="Quiz Title (required)" style="width:350px;"></div>
                        <div class="count-5" style="padding-left: 50px; margin-bottom:-15px; font-size: 15px;">Enter Quiz Description:
                            <textarea name="quizdescription" id="quizdescription" class="form-control" placeholder="Quiz Description (required) - Drag the lower right corner of the text area to toggle its size" style="width:500px; height:90px; min-height: 55px; min-width:320px; max-width:650px; max-height:177px;"></textarea>
                        </div>
                        <div class="count-6" style="padding-left: 50px; font-size: 20px;">
                            <b>Quiz Settings:</b>
                        </div>
                        <div class="count-7" style="padding-top: 15px; padding-left: 60px; font-size: 18px;">
                            Time limit:
                            <select name="istimelimit" id="istimelimit" style="font-size: 15px; margin-left: 5px;" onchange="toggletimelimit()">
                                <option value="Enabled">Enable</option>
                                <option value="Disabled">Disable</option>
                            </select> -
                            <input name="timelimit" id="timelimit" style="font-size: 15px; margin-left: 5px; width:60px;" type="number" min="1" max="180">
                            Minutes
                        </div>
                        <div class="count-7" style="padding-top: 12px; padding-left: 60px; font-size: 18px;">
                            Limit number of attempts per user:
                            <select name="isattemptlimit" id="isattemptlimit" style="font-size: 15px; margin-left: 5px;" onchange="toggleattemptlimit()">
                                <option value="Enabled">Enable</option>
                                <option value="Disabled">Disable</option>
                            </select> -
                            <input name="attemptlimit" id="attemptlimit" style="font-size: 15px; margin-left: 5px; width:60px;" type="number" min="1" max="10">
                            Attempts
                        </div>
                        <div class="count-8" style="padding-top: 12px; padding-left: 60px; font-size: 18px;">
                            Show questions:
                            <select name="isbacktrack" id="isbacktrack" style="font-size: 15px; margin-left: 5px;">
                                <option value="">(Select)</option>
                                <option value="2">All items at once</option>
                                <option value="1">One at a time, backtracking enabled</option>
                                <option value="0">One at a time, backtracking disabled</option>
                            </select>
                        </div>
                        <div class="count-9" style="padding-top: 12px; padding-left: 60px; font-size: 18px;">
                            Shuffle question order:
                            <select name="isshufflequestionorder" id="isshufflequestionorder" style="font-size: 15px; margin-left: 5px;">
                                <option value="">(Select)</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div class="count-10" style="padding-top: 12px; padding-left: 60px; font-size: 18px;">
                            Exam Mode:
                            <select name="isexammode" id="isexammode" style="font-size: 15px; margin-left: 5px;" onchange="toggleexammode()">
                                <option value="">(Select)</option>
                                <option value="1">Enable - Level 1</option>
                                <option value="2">Enable - Level 2</option>
                                <option value="0">Disable</option>
                            </select>
                        </div>
                        <div class="col-lg-6 exammodetext" style="padding-top: 12px; padding-left: 60px; font-size:13px; color:blue;">
                            Select an exam mode setting to view its description.
                        </div>
                        <div class="col-lg-5" style="padding-left: 60px;">
                            <button type="button" class="btn btn-success createquestion" style="text-align: center;width: 200px; height: 40px; border: 1px solid white; right: 150px; margin-top:15px;
                            font-size: 15px;" onclick="showtab(0,1)">
                                <b>Create new question</b>
                            </button>
                        </div>

                        <!-- end of quiz settings tab-->
                    </div>

                    <!-- questions tab -->
                    <div class="row maintab" style="display:none;">
                        <!-- question groups tab - jquery append at end of this tag-->
                        <div class="questions">

                            <!-- individual question tab -->
                            <div class="question">
                                <div class="row">
                                    <div class="count questionnum" style="padding-left: 35px;">Question 1</div>
                                </div>
                                <div class="count-7" style="padding-left: 60px; font-size: 18px;">Question Type:

                                    <!-- Question header -->
                                    <select name="questiontype[]" class="questiontype" style="font-size: 15px; margin-left: 5px;" onchange="togglequestiontype()">
                                        <option value="">(Select)</option>
                                        <option value="MCQ">Multiple Choice (Single answer)</option>
                                        <option value="MCMAQ">Multiple Choice (Multiple answers)</option>
                                        <option value="TF">True or False (Single statement)</option>
                                        <option value="MTF">True or False (Multiple statements)</option>
                                        <option value="FITBQ">Fill in the Blanks</option>
                                        <option value="ESSAY">Essay / Constructed Response</option>
                                    </select>
                                    &nbsp; &nbsp; Points: <input name="points[]" class="points" type="number" min="1" max="100" style="font-size: 15px; margin-left: 5px; width:60px;">
                                    <div class="count-7" style="padding-left: 2px; font-size: 18px; padding-top: 20px;">
                                        <div style="width:90px; display:inline-block;">Question: </div>
                                        <div style="display:-webkit-inline-box;">
                                            <textarea name="questiondescription[]" class="questiondescription form-control" placeholder="Question text (required) - Drag the lower right corner of the text area to toggle its size" style="width:480px; height:116px; min-height: 55px; min-width:320px; max-width:650px; max-height:177px;"></textarea>
                                        </div>
                                    </div>


                                    <!-- True or false (single statement) -->
                                    <div class="TF" style="padding-left: 2px; font-size: 18px; padding-top: 20px; display:none;">
                                        Select correct answer: &nbsp; &nbsp; &nbsp;
                                        <input type="radio" id="TFCorr0-1" name="TFcorr0" value="TRUE"><label for="TFCorr0-1"> &nbsp; TRUE</label> &nbsp; &nbsp; &nbsp;
                                        <input type="radio" id="TFCorr0-2" name="TFcorr0" value="FALSE"><label for="TFCorr0-2"> &nbsp; FALSE</label><br>
                                    </div>

                                    <!--True or False (multiple statements) -->
                                    <div class="MTF" style="padding-left: 2px; font-size: 18px; padding-top: 20px; display:none; max-width:750px;">
                                        Input statements and their states: <br>
                                        <input name="MTF0-1" class="form-control MTF0-1" style="display:inline; width:500px;" placeholder="Input first statement (required)">  &nbsp;
                                        <input type="radio" id="MTFCorr0-1" name="MTFcorr0-1" value="TRUE"><label for="MTFCorr0-1"> &nbsp; TRUE</label> &nbsp; &nbsp;
                                        <input type="radio" id="MTFCorr0-2" name="MTFcorr0-1" value="FALSE"><label for="MTFCorr0-2"> &nbsp; FALSE</label>
                                        <br>
                                        <input name="MTF0-2" class="form-control MTF0-2" style="display:inline; width:500px;" placeholder="Input second statement (required)">  &nbsp;
                                        <input type="radio" id="MTFCorr0-3" name="MTFcorr0-2" value="TRUE"><label for="MTFCorr0-3"> &nbsp; TRUE</label> &nbsp; &nbsp;
                                        <input type="radio" id="MTFCorr0-4" name="MTFcorr0-2" value="FALSE"><label for="MTFCorr0-4"> &nbsp; FALSE</label>
                                        <br>
                                        <div class="MTFstatement3" style="display:none">
                                            <input name="MTF0-3" class="form-control MTF0-3" style="display:inline; width:500px;" placeholder="Input third statement (required)">  &nbsp;
                                            <input type="radio" id="MTFCorr0-5" name="MTFcorr0-3" value="TRUE"><label for="MTFCorr0-5"> &nbsp; TRUE</label> &nbsp; &nbsp;
                                            <input type="radio" id="MTFCorr0-6" name="MTFcorr0-3" value="FALSE"><label for="MTFCorr0-6"> &nbsp; FALSE</label>
                                            <br>
                                        </div>
                                        <button type="button" class="addstatement btn btn-info" onclick="addMTFstatement()">Add another statement</button>
                                        <button type="button" class="removestatement btn btn-danger" onclick="removeMTFstatement()" style="display:none;">Remove statement</button><br>
                                        <br>
                                        Order of statements entered above will be shuffled during the quiz. <br>
                                        Options will be provided for the quiz takers to specifically identify which among the statemnents are true and false.
                                        <input type="hidden" name="numstatements0" value="2">
                                    </div>

                                    <!-- Multiple choice (single answer) -->
                                    <div class="MCQ" style="padding-left: 2px; font-size: 18px; padding-top: 20px; display:none;">
                                        Input option texts: (First two fields are required)<br>
                                        Option 1: <input name="MCQ1[]" class="form-control MCQ1" style="display:inline; width:500px;" placeholder="Option 1 (required)" onkeyup="toggleMCQ(1)"> &nbsp; <input type="radio" id="MCQCorr0-1" name="MCQcorr0" value="1" disabled><label for="MCQCorr0-1"> &nbsp; Correct answer</label><br>
                                        Option 2: <input name="MCQ2[]" class="form-control MCQ2" style="display:inline; width:500px;" placeholder="Option 2 (required)" onkeyup="toggleMCQ(2)"> &nbsp; <input type="radio" id="MCQCorr0-2" name="MCQcorr0" value="2" disabled><label for="MCQCorr0-2"> &nbsp; Correct answer</label><br>
                                        Option 3: <input name="MCQ3[]" class="form-control MCQ3" style="display:inline; width:500px;" placeholder="Option 3" onkeyup="toggleMCQ(3)"> &nbsp; <input type="radio" id="MCQCorr0-3" name="MCQcorr0" value="3" disabled><label for="MCQCorr0-3"> &nbsp; Correct answer</label> <br>
                                        Option 4: <input name="MCQ4[]" class="form-control MCQ4" style="display:inline; width:500px;" placeholder="Option 4" onkeyup="toggleMCQ(4)"> &nbsp; <input type="radio" id="MCQCorr0-4" name="MCQcorr0" value="4" disabled><label for="MCQCorr0-4"> &nbsp; Correct answer</label> <br>
                                        Option 5: <input name="MCQ5[]" class="form-control MCQ5" style="display:inline; width:500px;" placeholder="Option 5" onkeyup="toggleMCQ(5)"> &nbsp; <input type="radio" id="MCQCorr0-5" name="MCQcorr0" value="5" disabled><label for="MCQCorr0-5"> &nbsp; Correct answer</label> <br>
                                        Option order will be shuffled during quiz.
                                    </div>

                                    <!-- Multiple choice (multiple answers) -->
                                    <div class="MCMAQ" style="padding-left: 2px; font-size: 18px; padding-top: 20px; display:none;">
                                        Input option texts: (First three fields are required)<br>
                                        <div class="MAQ0">Option 1: <input name="MCMAQ0[]" class="form-control MCMAQ0-1" style="display:inline; width:500px;" placeholder="Option 1 (required)" onkeyup="toggleMCMAQ(1)"> &nbsp; <label><input type="checkbox" id="MCMAQCorr0-1" name="MCMAQcorr0-1" value="1" disabled> &nbsp; Correct answer</label></div>
                                        <div class="MAQ0">Option 2: <input name="MCMAQ0[]" class="form-control MCMAQ0-2" style="display:inline; width:500px;" placeholder="Option 2 (required)" onkeyup="toggleMCMAQ(2)"> &nbsp; <label><input type="checkbox" id="MCMAQCorr0-2" name="MCMAQcorr0-2" value="1" disabled> &nbsp; Correct answer</label></div>
                                        <div class="MAQ0">Option 3: <input name="MCMAQ0[]" class="form-control MCMAQ0-3" style="display:inline; width:500px;" placeholder="Option 3 (required)" onkeyup="toggleMCMAQ(3)"> &nbsp; <label><input type="checkbox" id="MCMAQCorr0-3" name="MCMAQcorr0-3" value="1" disabled> &nbsp; Correct answer</label></div>
                                        <div class="MAQ0">Option 4: <input name="MCMAQ0[]" class="form-control MCMAQ0-4" style="display:inline; width:500px;" placeholder="Option 4" onkeyup="toggleMCMAQ(4)"> &nbsp; <label><input type="checkbox" id="MCMAQCorr0-4" name="MCMAQcorr0-4" value="1" disabled> &nbsp; Correct answer</label></div>
                                        <div class="MAQ0">Option 5: <input name="MCMAQ0[]" class="form-control MCMAQ0-5" style="display:inline; width:500px;" placeholder="Option 5" onkeyup="toggleMCMAQ(5)"> &nbsp; <label><input type="checkbox" id="MCMAQCorr0-5" name="MCMAQcorr0-5" value="1" disabled> &nbsp; Correct answer</label></div>
                                        <button type="button" class="addMCMAA btn btn-info" onclick="addMCMAA()">Add another answer</button>
                                        <button type="button" class="removeMCMAA btn btn-danger" onclick="removeMCMAA()">Remove answer</button><br>
                                        Option order will be shuffled during quiz.
                                    </div>

                                    <!-- Fill in the blanks -->
                                    <div class="FITBQ" style="padding-left: 2px; font-size: 18px; padding-top: 20px; display:none;">
                                        Input accepted answer(s):<br>
                                        <input name="FITB0[]" class="form-control FITB0" style="display:inline; width:500px;" placeholder="Input answer text (required)"><br>
                                        <button type="button" class="addFITBA btn btn-info" onclick="addFITBA()">Add another answer</button>
                                        <button type="button" class="removeFITBA btn btn-danger" onclick="removeFITBA()" style="display:none;">Remove answer</button><br>
                                        Answers are neither case-sensitive nor space-sensitive.
                                    </div>

                                    <!-- Essay / constructed response -->
                                    <div class="ESSAY" style="padding-left: 2px; font-size: 18px; padding-top: 20px; display:none;">
                                        Responses from quiz takers will be recorded and are to be manually scored after the quiz.<br>
                                    </div>
                                </div>
                                <!-- end of individual questions tab -->
                            </div>

                            <!-- end of question groups tab-->
                        </div>
                        <br>

                        <div id="questionbuttons" class="row" style="padding-left: 60px; width:75%;">
                            Go to question:<span class="space"> &nbsp; </span>
                            <button type="button" class="questionnumbutton" style="background-color:dodgerblue; color: white; margin-bottom:10px; border-radius:5px;" onclick="showquestion(1)">1</button>
                        </div>
                        <br>
                        <div class="row" style="padding-left: 60px;">
                            <div style="float:left;">
                                <button type="button" id="addquestion" class="btn btn-success" onclick="addQuestion()"><b>Add a Question</b></button>
                                <button type="button" id="removequestion" class="btn btn-danger" style="display:none;" onclick="removeQuestion()"><b>Remove Question</b></button>
                                &nbsp; &nbsp; &nbsp;
                                <button type="button" id="prevquestion" class="btn btn-info" style="display:none;" onclick="showPrev()"><b>Previous</b></button>
                                <button type="button" id="nextquestion" class="btn btn-info" style="display:none;" onclick="showNext()"><b>Next</b></button>
                            </div>
                            <div style="float:right; margin-right:25%">
                                <button type="button" class="btn btn-success" onclick="showtab(1,0)"><b>View Quiz Settings</b></button>
                                <button type="button" class="btn btn-success" onclick="showtab(1,2)"><b>Finish Quiz</b></button>
                            </div>
                        </div>
                        <!-- end of questions tab-->
                    </div>

                    <!-- end screen tab -->
                    <div class="row maintab" style="display:none;">
                        <div class="row">
                            <div class="count" style="padding-left:30px;"> &nbsp;Final step: Finalize your quiz</div>
                        </div>
                        <div class="row">
                            <div style="padding-left:50px; margin: 0px 0px 12px 14px; font-size:20px; font-weight:bold;"> &nbsp;Total Questions: &nbsp; <span id=numquestions style="color:blue;"></span></div>
                            <div style="padding-left:50px; margin: 0px 0px 23px 14px; font-size:20px; font-weight:bold;"> &nbsp;Total Points: &nbsp; <span id=numpoints style="color:blue;"></span></div>
                        </div>
                        <div class="row" style="color:maroon">
                            <div class="count" style="padding-left:50px;"> &nbsp;Quiz code: &nbsp; <span id=quizcode1></span> &nbsp; <button type="button" class="btn btn-info btn-sm" onclick="generateCode()"><b>Regenerate code</b></button></div>
                            <input type="hidden" id="quizcode2" name="quizcode">
                        </div>
                        <div class="row" style="padding-left: 60px; font-size:18px;">
                            <div id="essayselect" style="margin: 0px 0px 14px 14px; display:none">
                                <!-- this dropdown menu pops up only when 
                                (1) there are essay questions, (2) question order shuffling is enabled -->
                                &nbsp; For essay questions:
                                <select id="showessays" name="showessays" style="font-size: 15px; margin-left: 5px;">
                                    <option value="1">Display at the last segment of the quiz</option>
                                    <option value="2">Shuffle together with other questions</option>
                                </select>
                            </div>
                            <div style="margin: 0px 0px 14px 14px;">
                                &nbsp; Accessibility of this quiz:
                                <select id="accessibility" name="accessibility" style="font-size: 15px; margin-left: 5px;" onchange="togglesharelink()">                                
                                    <option value="Enabled">Public (via quiz code)</option>
                                    <option value="Disabled">Private (only me)</option>
                                </select>
                            </div>
                            <div class="col-lg-12 quizlink" style="margin: 0px 0px 14px 14px; font-size:13px; color:blue; display:none;">
                                <button type="button" id="copyquiz" class="btn btn-sm btn-info" style="font-weight:bold;" onclick="copyquizlink()">Copy shareable link</button>
                            </div>
                            <div style="margin: 0px 0px 14px 14px;">
                                &nbsp; Allow viewing of questions after quiz:
                                <select id="isviewquestions" name="isviewquestions" style="font-size: 15px; margin-left: 5px;" onchange="toggleviewcorrectanswers()">
                                    <option value="Enabled">Yes</option>
                                    <option value="Disabled">No</option>
                                </select>
                            </div>
                            <div style="margin: 0px 0px 20px 14px;">
                                &nbsp; Show correct answers after quiz:
                                <select id="isshowcorrectanswers" name="isshowcorrectanswers" style="font-size: 15px; margin-left: 5px;">
                                    <option value="Enabled">Yes</option>
                                    <option value="Disabled">No</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" id="hasessay" name="hasessay">
                        <div class="row" id="essaywarning" style="display:none">
                            <div class='alert alert-info' role='alert' style='width:550px; margin-left:50px; font-size:13.5px; border-radius:10px;'><i class='icon_error-circle'></i>&nbsp; &nbsp;<b>
                                Only partial scores from objective-typed questions will be reflected immediately after the quiz taking sessions. The final score for the attempts will be shown only after you checked and scored the responses from them.</b>
                            </div>
                        </div>
                        <div class="row">
                            <div class="count-10" style="font-size: 14px; padding-left: 60px; font-weight: 700px;">
                                To edit quiz settings and questions, click either of the buttons below to return to editing,<br>
                                Once you are finished, click Save and Publish to <span style="color:blue;"><b>activate the quiz</b></span>.<br>
                                Note that the <span style="color:blue;"><b>questions</b></span> in this quiz <span style="color:blue;"><b>can no longer be edited</b></span> once it has been activated.<br><br>
                            </div>
                        </div>
                        <input type="hidden" name="userid" value="<?php echo $_SESSION['userid']; ?>">
                        <div class="row" style="padding-left: 60px;">
                            <div style="float:left;">
                                <button type="button" class="btn btn-success" onclick="showtab(2,0)"><b>Edit quiz settings</b></button>
                                <button type="button" class="btn btn-success" onclick="showtab(2,1)"><b>Edit Questions</b></button>
                            </div>
                            <div style="float:right; margin-right:25%">
                                <button type="button" class="btn btn-success publish" data-toggle="modal" data-target="#publishquizconfirm"><b>Save and Publish</b></button>
                            </div>
                        </div>
                        <!-- end of end screen tab -->
                    </div>

                </form>


            </section>
            <div id="adjust" style="height:25px">
                <br>
            </div>
        </section>
    </section>

    <input type="text" id="quizlink" style="position:fixed; top:-50px;">
    <!-- pop up alert -->
    <div style="position:fixed; top:80px; width:100%; padding-left:42px; z-index:6;">
        <div id="popupalert" class="alert alert-danger" style="border-radius:6px; width:440px; margin:auto; display:none; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">
            <i class='icon_error-circle'></i>&nbsp; &nbsp; <span id="alertcontent"></span>
        </div>
        <div id="popupsuccess" class="alert alert-success" style="border-radius:6px; width:320px; margin:auto; display:none; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">
            <i class='icon_check_alt'></i>&nbsp; &nbsp; <span id="successcontent"></span>
        </div>
    </div>

    <!-- modals for confirm publish quiz -->
    <div id="publishquizconfirm" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLongTitle" style="color:34aadc; font-weight:bold"><i class="icon_info"></i>&nbsp; &nbsp; Proceed to publish "<span id="quiztitle2"></span>"?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-content" style="padding:15px 20px; box-shadow:none; -webkit-box-shadow:none;">
                <b>Proceed to publish quiz? Keep in mind that your questions can <span style="color:#34aadc">no longer be modified</span> once your quiz has been activated.</b>
            </div>
            <div class="modal-footer" style="margin:0">
                <button type="button" class="btn btn-info" data-dismiss="modal"><b>Cancel and Edit</b></button>
                <button type="button" class="btn btn-success" data-dismiss="modal" onclick="submitform();"><b>Publish Quiz</b></button>
            </div>
        </div>
    </div>

</body>

</html>
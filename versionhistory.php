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
    <title>Version History | ANYQUIZ</title>
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
    <style>
        p,
        ul {
            font-size: 14px;
        }

        p {
            margin-left: 10px;
            margin-bottom: 5px;
            font-weight: bold;
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
        }

        h5 {
            margin-top: 20px;
            font-weight: bold;
            font-size: 15px;
        }

        .vlist li {
            position: relative;
            list-style: square !important;
            padding: 0px !important;
            margin: 0px !important;
        }

        [id^="versioninfodiv"] {
            display: none;
        }

        [id^="versioninfobtn"] {
            display: inline;
            position: relative;
            float: right;
            font-weight: bold;
            top: 15px;
            bottom: 10px;
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
                    <div class="col-sm-12">
                        <h3 style="margin: 20px 20px 10px 20px; font-weight:bold;"><i class="icon_info"></i>&nbsp; &nbsp; Version History</h3>
                    </div>
                </div>
                <!-- page start-->
                <div class="row">

                    <h4 style="margin:20px 0px 15px 30px;">Click the buttons to show the changes in every version.</h4>
                    <div class="col-lg-11" style="padding-left:50px; padding-right:30px;">
                        <div class="">

                            <div style="width:100%">
                                <h4>Version 2.1 (August 14, 2021) - Quiz feature update and UX improvements</h4>
                                <button id="versioninfobtn10" onclick="togglediv(10)" class="btn btn-info"><b><i class='icon_plus'></i> &nbsp; Expand</b></button>
                            </div>
                            <div id="versioninfodiv10">
                                <p>New quiz feature: True or false questions (multiple statements)</p>
                                <ul class="vlist">
                                    <li>Multiple true or false statements can be simultaneously placed in a question
                                        to be identified by the quiz takers.</li>
                                    <li>Options will be provided to the quiz takers for them to identify which specific
                                        statements among those given are true and which are not.</li>
                                    <li>The order of statements will be shuffled during the quiz; thus the correct answer option will vary.</li>
                                    <li>Points are awarded in full only if the correct option of selected; no partial points will be given.</li>
                                </ul>
                                <p>User experience improvements:</p>
                                <ul class="vlist">
                                    <li>"View" button will be shown in different colors to better distinguish between unscored and scored essay sets in a quiz.</li>
                                    <li>Question number buttons will turn green and red indicating correct and incorrect responses after submitting the quiz
                                        to easily locate wrong answers when reviewing.</li>
                                    <li>Some default quiz settings are placed and preselected to reduce extra clicks needed when configuring quiz settings for a new quiz.</li>
                                    <li>Added pagination to dashboard page - every page shows only 20 attempts at a time.</li>
                                </ul>
                                <p>Bug fixes:</p>
                                <ul class="vlist">
                                    <li>Fixed a bug where users with quizzes made could not properly access the dashboard page.</li>
                                    <li>Fixed a bug pertaining to issues with pagination for users with more than 20 total attempts made.</li>
                                    <li>Fixed an error where essay responses containing certain special characters such as &, +, and = 
                                        could not be stored correctly into the database, causing part of the response to not be seen.</li>
                                </ul>
                            </div>

                            <div style="width:100%">
                                <h4 style="color:#33be71;">Version 2.0 (July 5, 2021) - App enhancements, first public release</h4>
                                <button id="versioninfobtn9" onclick="togglediv(9)" class="btn btn-info"><b><i class='icon_plus'></i> &nbsp; Expand</b></button>
                            </div>
                            <div id="versioninfodiv9">
                                <p>The enhanced ANYQUIZ web application is now published online (anyquiz.me) for a year of public use!</p>
                                <p>Some bug fixes and app adjustments:</p>
                                <ul class="vlist">
                                    <li>Fixed an issue that caused the sign up success message to not pop up.</li>
                                    <li>Fixed navigation menu covering the top bar and unable to be toggled in certain pages when viewed using mobile.</li>
                                    <li>Fixed errors regarding misredirection of pages from certain buttons in the navigation menu.</li>
                                    <li>Fixed an issue that caused certain pages to fail to redirect to the maintenance page.</li>
                                    <li>Fixed an issue that caused some pending attempts to not show in the pending responses page.</li>
                                    <li>Fixed a rare issue that caused the quiz description to disappear after clicking "Take the Quiz" without the questions fully loaded.</li>
                                    <li>Fixed issues that triggered erroneous events and duplicate actions caused by instantaneous double clicking of certain buttons.</li>
                                    <li>Fixed an issue that prevented line breaks to be stored and reflected in essay responses.</li>
                                    <li>Fixed an issue that prevented users from entering new lines in their essay responses if all questions are shown at once.</li>
                                    <li>Finish quiz button will only show up at the last question when taking a quiz to prevent accidental submissions of incomplete answers.</li>
                                    <li>Added limit to quiz titles (64 characters) to avoid excessively long title displays in pages.</li>
                                </ul>
                                <p>We will be gradually adding these new features soon to provide you a better site experience:</p>
                                <ul class="vlist">
                                    <li>v2.2 - Image upload and rich text editor support</li>
                                    <li>v2.3 - Permament viewing of objective-type responses in quiz attempts</li>
                                    <li>v2.4 - Edit, delete, and append questions in an existing quiz</li>
                                    <li>v2.5 - Import questions from existing quizzes</li>
                                    <li>v2.7 - Groups panel where users can join and exclusively access certain quizzes</li>
                                    <li>v2.8 - Automatic email features and notifications</li>
                                    <li>v3.0 - Application appearance redesign</li>
                                </ul>
                                <p>A million thanks to all the beta testers and to everyone who supported and contributed throughout 
                                    the further development of this app. <br>May this project be of help to all of us as continuing students in spite of this pandemic!</p>
                            </div>

                            <div style="width:100%">
                                <h4>Version 1.9 (June 26, 2021) - Quiz feature updates</h4>
                                <button id="versioninfobtn8" onclick="togglediv(8)" class="btn btn-info"><b><i class='icon_plus'></i> &nbsp; Expand</b></button>
                            </div>
                            <div id="versioninfodiv8">
                                <p>New quiz feature: Essay / Constructed Response typed questions</p>
                                <ul class="vlist">
                                    <li>Users can choose to have open-ended responses for any question by clicking
                                        the "Essay / Constructed Response" question type.</li>
                                    <li>The limit for points per question is now increased to 100 points to increase quiz flexibility.</li>
                                    <li>While taking the quiz, text areas (with adjustable box height) will be provided for users to
                                        input their responses. Monospace fonts are applied in all text areas to increase answer readability
                                        as well as to support other formats such as code snippets.</li>
                                    <li>If a quiz has essay questions, only partial scores will be immediately shown after taking the quiz,
                                        while all such responses have to be scored manually in a separate page to determine the final score.</li>
                                    <li>Unlike all objective-typed questions, essay responses are permanently stored and visible to quiz takers
                                        unless the setting to view questions after submission is disabled in the quiz settings.</li>
                                    <li>Answer feedback for every response (optional) may be given and displayed to the quiz taker.</li>
                                    <li><b>Scoring rules:</b></li>
                                    <ul class="vlist2">
                                        <li>Partial credit of multiples of 0.5 (up to the maximum score allotted) can be given.</li>
                                        <li>If the text area is left blank, zero (0) points will be given be default.</li>
                                        <li>Scores given can be modified or adjusted even after initially submitting the scores.</li>
                                    </ul>
                                </ul>
                                <p>Dashboard and attempt log changes:</p>
                                <ul class="vlist">
                                    <li>Dashboard page now reflects the number of response sets to be scored.</li>
                                    <li>When clicked, a list of ungraded responses owned by the user will be displayed, whose items
                                        disappear once scores are given.</li>
                                    <ul class="vlist2">
                                        <li><b>Tip 1:</b> To stop receiving new or extra response sets from a quiz that is no longer active, users may
                                            de-activate the quiz either by changing its visibility to "Private" or by limiting the number of attempts
                                            in the "Edit Quiz Settings" page.</li>
                                    </ul>
                                    <li>Only completely scored quiz attempts will count towards the overall quiz statistics in the attempt log page.</li>
                                    <ul class="vlist2">
                                        <li><b>Tip 2:</b> To recheck a response set, simply access the quiz and click the
                                            "Recheck" button at the right side of the list item.</li>
                                    </ul>
                                </ul>
                            </div>

                            <div style="width:100%">
                                <h4>Version 1.8 (June 20, 2021) - Quiz feature updates and UI improvements</h4>
                                <button id="versioninfobtn7" onclick="togglediv(7)" class="btn btn-info"><b><i class='icon_plus'></i> &nbsp; Expand</b></button>
                            </div>
                            <div id="versioninfodiv7">
                                <p>New quiz feature #1: True or False questions (single statement)</p>
                                <ul class="vlist">
                                    <li>New multiple choice typed question added to save time creating T/F questions
                                        as well as reduce data storage and usage.</li>
                                    <li>Quiz makers will now only need to click either TRUE or FALSE as correct answers when
                                        selecting this question type.</li>
                                    <li>Scoring rules stay the same as regular multiple choice questions:</li>
                                    <ul class="vlist2">
                                        <li>Full points for every correct answer, no points for an incorrect or unselected answer.</li>
                                    </ul>
                                </ul>
                                <p>New quiz feature #2: Multiple choice questions (multiple answers)</p>
                                <ul class="vlist">
                                    <li>Users can now create multiple choice questions with multiple correct answers.</li>
                                    <li>Layout stays the same as regular multiple choice questions but with checkboxes
                                        allowing selection of multiple answer options.</li>
                                    <li>Scoring rules: (Right minus wrong implementation)</li>
                                    <ul class="vlist2">
                                        <li>Points will be divided by the number of correct answers present.</li>
                                        <li>For instance, if 6 points is allocated to the question and there are 4 correct answers,
                                            then each correct answer is worth 1.5 points.</li>
                                        <li>For every incorrect option selected, the same amount of points (1.5 points in above scenario) will be deducted for the question.</li>
                                        <li>If the number of incorrect answers selected >= that of
                                            correct answers selected, then no points will be given for the question.</li>
                                        <li>No deductions nor merits will be given for an unselected correct answer option.</li>
                                        <li>The final points to be shown and recorded will be rounded to the nearest multiple of 0.5
                                            (Example: 0.66 becomes 0.5, 1.14 becomes 1.0).</li>
                                    </ul>
                                </ul>
                                <p>Quiz interface improvements:</p>
                                <ul class="vlist">
                                    <li>Option orders for multiple choice questions are now shuffled.</li>
                                    <li>"Correct answer" labels and radio/checkbox are located beside every option instead
                                        of at the bottom to ease readability.</li>
                                    <li>Error messages are edited to be more specific in identifying the areas
                                        that the user has not been or incorrectly filled up.</li>
                                    <li>Answers in fill in the blanks questions are no longer space sensitive when checking.</li>
                                    <li>Revised code to improve data transmission efficiency when uploading and fetching quiz content.</li>
                                    <li>Slightly revised sample quiz to include the newly added question types.</li>
                                </ul>
                            </div>

                            <div style="width:100%">
                                <h4>Version 1.7 (June 14, 2021) - Content updates and bug fixes</h4>
                                <button id="versioninfobtn6" onclick="togglediv(6)" class="btn btn-info"><b><i class='icon_plus'></i> &nbsp; Expand</b></button>
                            </div>
                            <div id="versioninfodiv6">
                                <p>Content updates:</p>
                                <ul class="vlist">
                                    <li>Added terms of conditions page and forgot password modal.</li>
                                    <li>To retrieve an account with a forgotten pasword, users need to enter their username and email
                                        associated with the account and provide a brief information about their recent activity.</li>
                                    <li>Maintenance break page and 404 page (via HTTP ErrorDocument) added.</li>
                                    <li>Line breaks in text areas (quiz description and question description) ae now supported
                                        and displayed accordingly in the quiz taking page.</li>
                                    <li>Connection error alerts are added with an action to auto-reconnect to the server every
                                        10 seconds until a successful reconnection to the server is established.</li>
                                    <li>Minor layout fix in all pages to remove unnecessary scroll bars.</li>
                                </ul>
                                <p>Bug fixes in the quiz taking page from the previous major update:</p>
                                <ul class="vlist">
                                    <li>Fixed an error that the fifth option inputted in a multiple choice question is not displayed.</li>
                                    <li>Fixed a rare issue that some multiple choice answers were not recognized when checking quiz.</li>
                                    <li>Fixed an issue where in certain scenarios users are not able to resubmit quiz after encountering
                                        connection issues at the initial submission.</li>
                                    <li>Fixed an issue where the attempt time shown is exceeded by 2 seconds from the
                                        actual duration taken when the time limit is enabled.</li>
                                    <li>Fixed an issue where quiz navigation buttons were not hidden together with the questions
                                        after a failed quiz submission, making the questions reappear when the said buttons are clicked.</li>
                                </ul>
                            </div>

                            <div style="width:100%">
                                <h4>Version 1.6 (June 12, 2021) - Major security and integrity improvements</h4>
                                <button id="versioninfobtn5" onclick="togglediv(5)" class="btn btn-info"><b><i class='icon_plus'></i> &nbsp; Expand</b></button>
                            </div>
                            <div id="versioninfodiv5">
                                <p>Quiz taking page changes:</p>
                                <ul class="vlist">
                                    <li>Gathering of questions from the server now done when the user clicks the "Take the quiz"
                                        button instead of when the page loads. This eliminates the possibility of users downloading
                                        the page to gather the questions, answers, and options in advance.</li>
                                    <li>If a user gets disconnected while submitting a quiz, the timer now stops and the questions
                                        disappear, disallowing users to make changes to their answers and only permit resubmission
                                        once their connection restores.</li>
                                    <ul class="vlist2">
                                        <li>If time runs out and the submission fails due to connection issues, the questions will
                                            disappear to prohibit change of answers while waiting for reconnection to server.</li>
                                    </ul>
                                    <li>Questions in the quiz taking page will now be completely removed after the following conditions:</li>
                                    <ul class="vlist2">
                                        <li>If the setting to view questions after the submission is disabled.</li>
                                        <li>If the user exits the quiz taking page in an invalid manner (focus loss or full-screen exit in exam mode).</li>
                                    </ul>
                                </ul>
                                <p>App integrity improvements:</p>
                                <ul class="vlist">
                                    <li>Button in the sign-up completion modal now redirects users to the log in page
                                        instead of closing the modal and having users click the button in the main tab manually.</li>
                                    <li>Successful quiz submission page modified to avoid possible form resubmission which may
                                        duplicate entries of the same quiz and question set.</li>
                                    <li>Invalid and unfinished attempts will no longer be included in the computation of the
                                        general quiz statistics (low, mean, and high scores).</li>
                                </ul>
                            </div>

                            <div style="width:100%">
                                <h4>Version 1.5 (June 9, 2021) - Quality updates and bug fixes</h4>
                                <button id="versioninfobtn4" onclick="togglediv(4)" class="btn btn-info"><b><i class='icon_plus'></i> &nbsp; Expand</b></button>
                            </div>
                            <div id="versioninfodiv4">
                                <p>Quality of service updates:</p>
                                <ul class="vlist">
                                    <li>Revamped the appearances of the Sign Up, Log In, and Fetch Quiz pages.</li>
                                    <li>Added a welcome modal with feature summary to be displayed upon signing in of the user.</li>
                                    <li>Added quiz analytics in the quiz info view - Lowest, mean, and highest scores.</li>
                                    <li>Added displays to number of points obtained per question in the quiz results.</li>
                                    <li>Added pop-up message that displays a successful quiz submission (for valid attempts) afer taking the quiz.</li>
                                    <li>Added buttons to copy quiz link to clipboard for direct sharing to other users.</li>
                                    <li>Added icons to most buttons for better appearance.</li>
                                    <li>Added button to relaunch sample quiz, located in the My Profile page.</li>
                                    <li>Added title bar icon and meta description tags for easier site searching and recognition.</li>
                                    <li>Added support for math expressions via LaTeX</li>
                                    <li>Alert messages for removing attempt records now include attempt number, username, and attempt datetime displayed.</li>
                                    <li>Removed and replaced all JS alert messages with in-site modals.</li>
                                    <li>Disabled the action to submit quiz after hitting "Enter" key to prevent accidental submissions.</li>
                                </ul>
                                <p>Bug fixes:</p>
                                <ul class="vlist">
                                    <li>Fixed an issue causing some alert messages to be not displayed upon encountering errors.</li>
                                    <li>Fixed an issue causing passwords to be incorrectly reconfigured after updating their account information.</li>
                                    <li>Fixed an issue causing success and failure messages to overlap when triggered at nearly the same time.</li>
                                    <li>Fixed a bug that allowed displays of blank score and duration entries for unfinished attempt records.</li>
                                    <li>Fixed a bug that allowed displays of quiz analytics with blank values if there are no attempts yet on the quiz.</li>
                                    <li>Fixed a bug that allowed few users to bypass the fullscreen check right before beginning quizzes with Level 2 Exam Mode enabled
                                        without getting an invalid attempt pop-up alert message.</li>
                                    <li>Fixed a bug that caused users to be stuck in the transition to the results screen after an infinite appearance of alert messages
                                        caused by a loss of tab focus in a Exam Mode Level 2- Enabled quizzes.</li>
                                </ul>
                            </div>

                            <div style="width:100%">
                                <h4>Version 1.4 (June 8, 2021) - Feature and quality of service updates</h4>
                                <button id="versioninfobtn3" onclick="togglediv(3)" class="btn btn-info"><b><i class='icon_plus'></i> &nbsp; Expand</b></button>
                            </div>
                            <div id="versioninfodiv3">
                                <p>New quiz feature #1: Attempt monitoring and management</p>
                                <ul class="vlist">
                                    <li>Added quiz setting to limit number of attempts per user.</li>
                                    <li>When enabled, only the first N results (regardless of the status) will be recorded.
                                        Users can still retake the quiz even after exceeding the attempt limit but their results
                                        will no longer be appended and reflected.</li>
                                    <li>Alert messages and displays are made throughout the quiz taking page to remind the quiz takers
                                        of the attempt limit.</li>
                                    <li>Attempt records can now be removed by the quiz creator in the quiz info page, which also affects
                                        the accumulated attempt count of the particular user whose record is being deleted.</li>
                                </ul>
                                <p>New quiz feature #2: Exam mode (with fullscreen requirement)</p>
                                <ul class="vlist">
                                    <li>New exam mode with better security featuring a requirement for the users to be in full screen before
                                        and during the quiz attempt! An attempt to exit full screen will result in an invalid attempt with no
                                        points recorded.</li>
                                    <li>This is labeled as Level 2 in the exam mode setting, while the old exam mode will be labeled Level 1.</li>
                                </ul>
                                <p>New user feature: Edit quiz settings</p>
                                <ul class="vlist">
                                    <li>Users can now modify the quiz settings, appearance, and behavior of their created quizzes in the
                                        quiz info page.</li>
                                    <li>Questions in the quiz remain locked from modifying.</li>
                                </ul>
                                <p>Quality of service updates:</p>
                                <ul class="vlist">
                                    <li>Users can now remove the last question being added in the quiz making page, resolving an issue where
                                        accidental clicks of the "Add new question" button may occur when undesired, forcing the users to input
                                        content to the extra question.</li>
                                    <li>Adjusted the quiz timer to support time lengths of more than an hour. Time limit maximum value increased
                                        from 60 minutes to 180 minutes.</li>
                                    <li>Set the minimum width for option labels to reduce difficulty of clicking an option whenever the option text
                                        is short (i.e. a number).</li>
                                    <li>Added a display on the total number of quizzes made in the dashboard.</li>
                                </ul>
                                <p>Fixes and security updates:</p>
                                <ul class="vlist">
                                    <li>Fixed a bug causing the integrity check to be bypassed when entering decimal values to the time limit,
                                        attempt limit, and points per question fields.</li>
                                    <li>The quiz info and quiz editing pages now include additional cross-checks on the following:</li>
                                    <ul class="vlist2">
                                        <li>Whether the quiz_id, quiz_title, and quiz_code all match to the same existing quiz in the database to
                                            ensure the serialized URL being transmitted is accurate to prevent erroneous events when updating
                                            quiz records or settings.</li>
                                        <li>Whether the from_user_id in the database matches the current session's userid to ensure settings and
                                            analytics of the quizzes can only be accessed by their corresponding owners.</li>
                                    </ul>
                                </ul>
                            </div>

                            <div style="width:100%">
                                <h4>Version 1.2 (June 6, 2021) - Feature, security, and quality of service updates</h4>
                                <button id="versioninfobtn2" onclick="togglediv(2)" class="btn btn-info"><b><i class='icon_plus'></i> &nbsp; Expand</b></button>
                            </div>
                            <div id="versioninfodiv2">
                                <p>New quiz features:</p>
                                <ul class="vlist">
                                    <li>Option to shuffle question order when loading the quiz.</li>
                                    <li>Option to allow viewing of questions after quiz submission of user.</li>
                                    <li>Option to show/hide correct answers after quiz submission of user.</li>
                                    <li>Option to show all questions at once during the quiz.</li>
                                </ul>
                                <p>Quality of service updates:</p>
                                <ul class="vlist">
                                    <li>Change the background color of the quiz timer to red when timer is below 3 minutes
                                        to increase user alertness of the time remaining.</li>
                                    <li>Replaced most JS alert messages with in-site pop-up alert messages.</li>
                                    <li>Error messages are edited and made clearer.</li>
                                    <li>Disabled all page navigation links in the quiz taking page during an ongoing quiz (has started and not yet ended).</li>
                                    <li>Removed the unnecessary JS links to decrease loading time.</li>
                                    <li>Improved the appearances of the dashboard page and about page.</li>
                                    <li>Removing a quiz now redirects the user to quizzes page instead of home page.</li>
                                    <li>Added alert message to users leaving the create quiz page to avoid accidental loss of progress.</li>
                                    <li>Adjusted animation speeds across pages.</li>
                                </ul>
                                <p>Fixes:</p>
                                <ul class="vlist">
                                    <li>Resolved an issue where "Confirm form resubmission" messages are triggered in some pages
                                        due to incorrectly set form methods.</li>
                                    <li>Resolved scrolling related issues by updating the JS file jquery.nicescroll.js.</li>
                                    <li>Resolved an issue where the quiz info page erroneously loads due to the absence of a JS page
                                        redirect command when there is no $_POST data being set.</li>
                                </ul>
                                <p>Other updates:</p>
                                <ul class="vlist">
                                    <li>Revamped the Sample Quiz to make it more user-friendly. Its quiz code is now displayed to all first-time users.</li>
                                    <li>Record of quiz results to be appended to the database when the quiz begins instead of when
                                        the quiz ends, then updated to the database with the scores and durations when the quiz ends.</li>
                                    <ul class="vlist2">
                                        <li>This is to prevent attempt escapes when a user reloads or closes the page
                                            for the intention of escaping from an unrecorded attempt.</li>
                                        <li>An unfinished attempt will be initially recorded, and stays there if the attempt is not
                                            completed. It will be then otherwise updated with the durations and scores.</li>
                                    </ul>
                                </ul>
                            </div>

                            <div style="width:100%">
                                <h4 style="color:#33be71;">Version 1.0 (June 4, 2021) - Initial release (Project submission)</h4>
                                <button id="versioninfobtn1" onclick="togglediv(1)" class="btn btn-info"><b><i class='icon_plus'></i> &nbsp; Expand</b></button>
                            </div>
                            <div id="versioninfodiv1">
                                <p>Initial features:</p>
                                <ul class="vlist">
                                    <li>Account creation and manipulation</li>
                                    <li>Quiz settings: Time limit, backtracking, page focus monitoring,
                                        quiz visibility, points per question, access code generation</li>
                                    <li>Question types supported:</li>
                                    <ul class="vlist2">
                                        <li>Multiple choice (5 option slots available, at least 2 must be enabled)</li>
                                        <li>Fill in the blanks (up to 10 accepted answers, case insensitive but space sensitive)</li>
                                    </ul>
                                    <li>Insertion to and retrieval from database done only once per quiz making or
                                        quiz taking session.</li>
                                    <ul class="vlist2">
                                        <li>Faster loading time and less network traffic.</li>
                                    </ul>
                                    <li>Saving of results (time and score) for every quiz attempt from the user.</li>
                                    <li>Viewing of user-created quizzes and attempt summary.</li>
                                    <li>Integrity checks for all inputs throughout the application to prevent
                                        transmission of empty values for required information.</li>
                                    <li>Activated user feedback form.</li>
                                </ul>
                            </div>

                            <h5 style="margin-right:45px;">Should you have any suggestions, feedbacks, or feature requests, feel free to send a
                                message to us in the feedback form or directly to the developers through their contact info provided.
                                Your active presence in this site truly means a lot to us!
                            </h5>
                            <br>

                            <a href="about.php"><button class="btn btn-info"><b>Back</b></button></a>
                            <a href="index.php"><button class="btn btn-info" style="margin-left:10px;"><b>Dashboard</b></button></a>
                            <div style="height:50px">
                            </div>
                        </div>
                    </div>
            </section>
        </section>
    </section>

    <!--main content end-->
    </section>
    <!-- container section start -->

    <script>
        function togglediv(x) {
            if ($('#versioninfodiv' + x).is(':hidden')) {
                $('#versioninfodiv' + x).show(300);
                $('#versioninfobtn' + x).html("<b><i class='icon_minus-06'></i> &nbsp; Collapse</b>");
            } else {
                $('#versioninfodiv' + x).hide(300);
                $('#versioninfobtn' + x).html("<b><i class='icon_plus'></i> &nbsp; Expand</b>");
            }
        }

        $('document').ready(function() {
            $('html').getNiceScroll().remove();
            $('html').css({
                "overflow-x": "hidden"
            });
        });
    </script>

</body>

</html>
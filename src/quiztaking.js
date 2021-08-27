/*----------------------------------------------------

  JS File for the quiz taking page (ANYQUIZ) v2.0
  By Wayne Dayata
  July 5, 2021

----------------------------------------------------*/
var xhttp;
var urlData;

/* toggle show/hide of tabs - main tabs (quiz settings, question tabs, end screen) */

var visibleDiv = 0; /* current question number (0 to N-1) */
var isdone = 0; /* 1 if quiz is finished */
var time = 0;
var quizinprogress = 0; /* if submit button is clicked then quizinprogress is back to 0 */

/* exam mode checker variables */
var isCheating = 0; /* tab focus checker */
var a = 0;

/* for essay - response insertion to db */
var attemptid;
var response;
var hasresponse = 0;
var urlData2;
var res2;

/* for MCMAQ - helper variables in checking */
var numcorrans;
var numcorrans_selected;
var numwrongans_selected;
var partialscore;
var ctr;

/*-----------------------------------------------*/

function showtab(fromtab, totab) {

    if (fromtab == 0 && totab == 1) {
        //retrieve questions and append to the php page
        $('#takethequiz').prop("disabled", true);
        $('#takethequiz').html("Loading questions...");
        xhttp = new XMLHttpRequest();
        var url = "./src/fetchquestions.php";
        urlData = url + "?quizid=" + quizid + "&numquestions=" + numquestions + "&isshufflequestionorder=" + isshufflequestionorder + "&isbacktrack=" + isbacktrack;
        xhttp.open("POST", urlData, true);
        xhttp.send();
        xhttp.onreadystatechange = function () {
            //check if AJAX is successful before starting quiz
            if (this.readyState == 4 && this.status == 200) {

                //1. parse the object, then insert questions
                res = JSON.parse(this.responseText);
                $('.questions').append(res["data"]);

                //2. begin the quiz

                quizinprogress = 1;
                window.scrollTo(0, 0);
                a = 5;
                MathJax.Hub.Queue(["Typeset", MathJax.Hub, "questionlist"]);
                if (timelimit > 0) {
                    $("#timeralert").css({
                        "background-color": "#d1ecf1",
                        "color": "#0c5460",
                        "border-color": "#3cb1c3"
                    });
                    $('#timercomment').html("Remaining time: ");
                    if (timelimit >= 60) {
                        $('#timer').html(parseInt(timelimit / 60) + ":" + ((timelimit % 60 > 9) ? timelimit % 60 : "0" + timelimit % 60) + ":01");
                    } else {
                        $('#timer').html((timelimit > 9 ? timelimit : "0" + timelimit) + ":01");
                    }
                    startTimerDown();
                } else {
                    $('#timercomment').html("Time elapsed: ");
                    $('#timer').html("00:00");
                    startTimerUp();
                }
                if (isbacktrack == 0) {
                    $("#questionbuttons").hide();
                    $('#finishquiz').hide();
                    showquestion(1);
                } else if (isbacktrack == 1) {
                    $('#finishquiz').hide();
                    $("#questionbuttons").show();
                    showquestion(1);
                } else { //isbacktrack == 2
                    $("#questionbuttons").hide();
                    $('button#prevquestion').hide();
                    $('button#nextquestion').hide();
                }
                $('#timeralert').show();
                $(".maintab").hide();
                $(".maintab:eq(" + totab + ")").fadeIn(600);
            }
        };
    }
    if (totab == 2) {
        if (isdone == 0) {
            $(".quizTitle").html("Result for " + $(".quizTitle").html());
            $('#finishquiz').html("Submitting...");
            $('#finishquiz').prop("disabled", true);
        }
        isdone = 1;
        $("#questionbuttons").show();
        $("#timeralert").hide();
        /* disable all inputs, then check quiz */
    }
    if (fromtab == 2 && totab == 1) {
        window.scrollTo(0, 0);
        if (isbacktrack != 2) {
            showquestion(1);
        } else {
            $("#questionbuttons").hide();
            $('button#prevquestion').hide();
            $('button#nextquestion').hide();
        }
    }

    if (!(fromtab == 0 && totab == 1)) {
        $(".maintab").hide();
        $(".maintab:eq(" + totab + ")").fadeIn(600);
    }
}


/* toggle show/hide of tabs - question tabs (Q 1 to N)*/
function showDiv() {
    $(".question").hide();
    $(".question:eq(" + visibleDiv + ")").fadeIn(400);
    checkCurrTab();
}
showDiv()

function showNext() {
    if (visibleDiv != $(".question").length - 1) {
        //prevent double clicking
        $('button#nextquestion').prop("disabled", true);
        setTimeout(function () { $('button#nextquestion').prop("disabled", false); }, 200);
        visibleDiv++;
    }
    showDiv();
}

function showPrev() {
    if (visibleDiv != 0) {
        //prevent double clicking
        $('button#prevquestion').prop("disabled", true);
        setTimeout(function () { $('button#prevquestion').prop("disabled", false); }, 200);
        visibleDiv--;
    }
    showDiv();
}

function showquestion(x) {
    visibleDiv = x - 1;
    showDiv();
}

function checkCurrTab() {
    if (!(isbacktrack == 2 && isdone == 0)) {
        if (visibleDiv == $(".question").length - 1) {
            $('button#nextquestion').hide();
            $('#finishquiz').show();
        } else {
            $('button#nextquestion').show();
            $('#finishquiz').hide();
        }

        if (visibleDiv == 0) {
            $('button#prevquestion').hide();
        } else if (isbacktrack == 1 || isdone == 1) {
            $('button#prevquestion').show(); //back button shown only when backtracking enabled
        }
    }
    if (isdone == 1){
        $('#finishquiz').hide();
    }
}

/* toggle time limit */
var h, m, s;
function startTimerDown() {
    /* given time should be already set in the quiz page - (hour):(min):01 for countdown and 0:00 for countup */
    var presentTime = $('#timer').html();
    var timeArray = presentTime.split(/[:]+/);
    if (timeArray.length == 3) {
        h = timeArray[0];
        m = parseInt(timeArray[1]);
        s = checkSecondDown(parseInt(timeArray[2]) - 1);
    } else {
        h = 0;
        m = parseInt(timeArray[0]);
        s = checkSecondDown(parseInt(timeArray[1]) - 1);
    }
    if (s == 59) {
        m = m - 1;
    }
    if (m < 0 && s == 59) {
        h = h - 1;
        m = 59;
    }
    if (m < 10) {
        m = "0" + m;
    }
    if (h == 0 && m < 3) {
        if (quizinprogress != 2) {
            timealert();
        }
    }
    if (h < 0) {
        /* stop quiz attempt, proceed to checking */
        $('#timer').html("00:00");
        time--;
        $('#outoftimeerror').modal('show');
        $('#quiz').submit();
    }
    if (quizinprogress != 2) {
        if (h > 0) {
            $('#timer').html(h + ":" + m + ":" + s);
        } else if (h == 0) {
            $('#timer').html(m + ":" + s);
        }
        setTimeout(startTimerDown, 1000);
    }
}

function checkSecondDown(sec) {
    if (sec < 10 && sec >= 0) {
        sec = "0" + sec;
    }; // add zero in front of numbers < 10
    if (sec < 0) {
        sec = "59"
    };
    time++;
    return sec;
}

function startTimerUp() {
    /* given time should be already set in the quiz page - (hour):(min):01 for countdown and 0:00 for countup */
    var presentTime = $('#timer').html();
    var timeArray = presentTime.split(/[:]+/);
    var h, m, s;
    if (timeArray.length == 3) {
        h = timeArray[0];
        m = parseInt(timeArray[1]);
        s = checkSecondUp(parseInt(timeArray[2]) + 1);
    } else {
        h = 0;
        m = parseInt(timeArray[0]);
        s = checkSecondUp(parseInt(timeArray[1]) + 1);
    }
    if (s == 0) {
        m = (parseInt(m) + 1);
    }
    if (m == 60) {
        h = h + 1;
        m = 0;
    }
    if (m < 10) {
        m = "0" + m;
    }
    if (quizinprogress != 2) {
        if (h > 0) {
            $('#timer').html(h + ":" + m + ":" + s);
        } else {
            $('#timer').html(m + ":" + s);
        }
        setTimeout(startTimerUp, 1000);
    }
}

function checkSecondUp(sec) {
    if (sec < 10 && sec >= 0) {
        sec = "0" + sec;
    } // add zero in front of numbers < 10
    if (sec > 59) {
        sec = "00";
    }
    time++;
    return sec;
}

function timealert() {
    $("#timeralert").css({
        "background-color": "#f2dede",
        "color": "#b30021",
        "border-color": "#cd7e8a"
    });
}

/*sending the token */
function sendtoken(e) {
    if (isvalidattempt == 1) {
        e.preventDefault();
        xhttp = new XMLHttpRequest();
        var url = "./src/insertattempttoken.php";
        urlData = url + "?quizid=" + quizid;
        xhttp.open("POST", urlData, true);
        xhttp.send();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                $('#takethequiz').prop("disabled", true);
                $('#takethequiz').html("Loading questions...");
                //proceed to retrieve questions and show question tab
                var res = JSON.parse(this.responseText);
                attemptid = res['attemptid'];
                showtab(0, 1);
            }
        };
    } else {
        //proceed to retrieve questions and show question tab
        showtab(0, 1);
    }
}

/*checking the quiz */
var x, y, temp1, temp2, temp3, score = 0,
    comment = "",
    questionsanswered = 0,
    commentadded = 0;
useranswer = "";

function checkQuiz(e) {
    e.preventDefault();
    quizinprogress = 2; //stop timer and change to green
    if (timelimit > 0) time--;
    if (time < 0) time = 0;
    $("#timeralert").css({
        "background-color": "#dff0d8",
        "color": "#468847",
        "border-color": "#6ea540"
    });

    //disable buttons and inputs to begin checking
    $('#finishquiz').html("Submitting...");
    $('#finishquiz').prop("disabled", true);
    $('input').prop("disabled", true);
    $('select').prop("disabled", true);
    $('textarea').prop("disabled", true);
    $(".question").hide(); //cannot see the questions anymore and adjust answers 
    //since submit button is clicked and time is stopped

    //check every answer
    var comment1 = "<br><div class='alert alert-success comment' role='alert' style='width:550px; margin-left:50px; display:none;'><i class='icon_check_alt'></i>&nbsp; &nbsp;<b>Correct! Well done!</b>"
    var comment2 = "<br><div class='alert alert-danger comment' role='alert' style='width:550px; margin-left:50px; display:none;'><i class='icon_close_alt'></i>&nbsp; &nbsp;<b>Correct answer/s: </b>"
    var comment3 = "<br><div class='alert alert-danger comment' role='alert' style='width:550px; margin-left:50px; display:none;'><i class='icon_close_alt'></i>&nbsp; &nbsp;<b>Your answer is incorrect.</b>"
    var comment4 = "<br><div class='alert alert-warning comment' role='alert' style='width:550px; margin-left:50px; display:none;'><i class='icon_error-circle'></i>&nbsp; &nbsp;<b>Your answer is partially correct.</b>"
    var comment5 = "<br><div class='alert alert-info comment' role='alert' style='width:550px; margin-left:50px; display:none;'><i class='icon_info'></i>&nbsp; &nbsp;<b>Not yet graded: </b>Your response will be checked soon."
    var comment6 = "<br><div class='alert alert-danger comment' role='alert' style='width:550px; margin-left:50px; display:none;'><i class='icon_close_alt'></i>&nbsp; &nbsp;<b>You have not entered any response for this question.</b>"
    for (x = 0; x < ($('.question').length); x++) {

        /* True or false (single statement) */
        if ($(".questiontype:eq(" + x + ")").html() == "TF") {

            /* check if correct answer option is checked */
            if ($('#TF' + (x + 1) + '-' + $('#ans' + (x + 1)).html()).is(':checked')) {
                comment = comment1;
                score += (parseInt($(".points:eq(" + x + ")").html()));
                $(".obtainedpoints:eq(" + x + ")").html($(".points:eq(" + x + ")").html() + "/");
                $('.questionnumbutton:eq('+ x +')').css("background-color","#32d264");
            } else {
                if (isshowcorrectanswers) {
                    comment = comment2;
                    comment = comment.concat($('#ans' + (x + 1)).html());
                } else {
                    comment = comment3;
                }
                $(".obtainedpoints:eq(" + x + ")").html("0/");
                $('.questionnumbutton:eq('+ x +')').css("background-color","#ff0808");
            }

            /* check if answered */
            if ($('#TF' + (x + 1) + "-TRUE").is(':checked') || $('#TF' + (x + 1) + "-FALSE").is(':checked')) {
                questionsanswered++;
            } else {
                $('.questionnumbutton:eq('+ x +')').css("background-color","#a0a0a0");
            }


        }

        
        /* True or false (multiple statements) */
        if ($(".questiontype:eq(" + x + ")").html() == "MTF") {

            /* check if correct answer option is checked */
            if ($('#MTF' + (x + 1)).val() == parseInt($('#ans' + (x + 1)).html())) {
                comment = comment1;
                score += (parseInt($(".points:eq(" + x + ")").html()));
                $(".obtainedpoints:eq(" + x + ")").html($(".points:eq(" + x + ")").html() + "/");
                $('.questionnumbutton:eq('+ x +')').css("background-color","#32d264");
            } else {
                if (isshowcorrectanswers) {
                    comment = comment2;
                    comment = comment.concat($('#MTF'+(x+1)+' option[value='+ $('#ans' + (x + 1)).html() +']').html());
                } else {
                    comment = comment3;
                }
                $(".obtainedpoints:eq(" + x + ")").html("0/");
                $('.questionnumbutton:eq('+ x +')').css("background-color","#ff0808");
            }

            /* check if answered */
            if ($('#MTF' + (x + 1)).val() != -1) {
                questionsanswered++;
            } else {
                $('.questionnumbutton:eq('+ x +')').css("background-color","#a0a0a0");
            }

        }

        /* Multiple choice - single answer */
        if ($(".questiontype:eq(" + x + ")").html() == "MCQ") {

            /* check if correct answer option is checked */
            if ($('#MCQ' + (x + 1) + '-' + $('#ans' + (x + 1)).html()).is(':checked')) {
                comment = comment1;
                score += (parseInt($(".points:eq(" + x + ")").html()));
                $(".obtainedpoints:eq(" + x + ")").html($(".points:eq(" + x + ")").html() + "/");
                $('.questionnumbutton:eq('+ x +')').css("background-color","#32d264");
            } else {
                if (isshowcorrectanswers) {
                    comment = comment2;
                    comment = comment.concat($('label[for=MCQ' + (x + 1) + '-' + $('#ans' + (x + 1)).html() + ']').html());
                } else {
                    comment = comment3;
                }
                $(".obtainedpoints:eq(" + x + ")").html("0/");
                $('.questionnumbutton:eq('+ x +')').css("background-color","#ff0808");
            }
            
            /* check if answered */
            if ($('#MCQ' + (x + 1) + "-1").is(':checked') || $('#MCQ' + (x + 1) + "-2").is(':checked') || $('#MCQ' + (x + 1) + "-3").is(':checked') || $('#MCQ' + (x + 1) + "-4").is(':checked') || $('#MCQ' + (x + 1) + "-5").is(':checked')) {
                questionsanswered++;
            } else {
                $('.questionnumbutton:eq('+ x +')').css("background-color","#a0a0a0");
            }
        }

        /* Multiple choice - multiple answers */
        if ($(".questiontype:eq(" + x + ")").html() == "MCMAQ") {

            /* initialize variables */
            numcorrans = 0;
            numcorrans_selected = 0;
            numwrongans_selected = 0;
            partialscore = 0.0;

            temp1 = $('#ans' + (x + 1)).html();
            $('#ans' + (x + 1)).html(temp1.replace(/,\s*$/, "")); //remove final comma

            /* check answer options */
            for (ctr = 1; ctr <= 9; ctr++) {

                if (parseInt($('#MCMAQ' + (x + 1) + "-" + ctr).val()) == 1) {
                    numcorrans++;
                    if ($('#MCMAQ' + (x + 1) + "-" + ctr).is(":checked")) {
                        numcorrans_selected++;
                    }

                } else if (parseInt($('#MCMAQ' + (x + 1) + "-" + ctr).val()) == 0) {
                    if ($('#MCMAQ' + (x + 1) + "-" + ctr).is(":checked")) {
                        numwrongans_selected++;
                    }
                }

            }

            /* compute points and add comments*/
            if (numcorrans_selected == numcorrans && numwrongans_selected == 0) {
                //perfect score
                partialscore = parseInt($(".points:eq(" + x + ")").html());
                comment = comment1;
                $('.questionnumbutton:eq('+ x +')').css("background-color","#32d264");
            } else {
                //not perfect score
                $('.questionnumbutton:eq('+ x +')').css("background-color","#ff0808");
                if ((numcorrans_selected == 0 && numwrongans_selected == 0) || numwrongans_selected > numcorrans_selected) {
                    //no points
                    partialscore = 0;
                } else {
                    if (numcorrans_selected < numcorrans && numwrongans_selected > 0) {
                        numwrongans_selected -= 0.4;
                    }
                    //compute for partial score
                    //round(points for the item / numcorrans * 2 * (corrselected - wrongselected)) / 2.0
                    partialscore = (Math.round(parseInt($(".points:eq(" + x + ")").html()) / numcorrans * 2 * (numcorrans_selected - numwrongans_selected))) / 2.0;
                }

                //determine comments
                if (isshowcorrectanswers) {
                    comment = comment2;
                    $('#ans' + (x + 1)).html($('#ans' + (x + 1)).html().replace(/,/g, ', ')); //add spaces to commas
                    comment = comment.concat($('#ans' + (x + 1)).html());
                } else {
                    if ((numcorrans_selected == 0 && numwrongans_selected == 0) || numwrongans_selected > numcorrans_selected) {
                        //totally incorrect
                        comment = comment3;
                    } else {
                        //partially correct
                        comment = comment4;
                    }
                }

            }

            /* check if answered */
            if (numcorrans_selected > 0 || numwrongans_selected > 0) {
                questionsanswered++;
            } else {
                $('.questionnumbutton:eq('+ x +')').css("background-color","#a0a0a0");
            }

            /* append points */
            score += partialscore;
            $(".obtainedpoints:eq(" + x + ")").html(partialscore + "/");

        }

        /* Fill in the blanks */
        if ($(".questiontype:eq(" + x + ")").html() == "FITBQ") {

            /* separate correct answers embedded, check for matches one by one */
            temp1 = $('#ans' + (x + 1)).html();
            $('#ans' + (x + 1)).html(temp1.replace(/,,\s*$/, "")); //remove final comma set
            temp1 = temp1.replace(/,,\s*$/, "");
            temp2 = temp1.split(",,");

            useranswer = $('[name=FITBQ' + (x + 1) + ']').val();
            useranswer = useranswer.replace(/\s+/g, '');
            //space insensitive checking
            isMatch = 0;
            for (y = 0; y < temp2.length && isMatch == 0; y++) {
                temp3 = temp2[y].replace(/\s+/g, '');
                if (temp3.localeCompare((useranswer), undefined, {
                    sensitivity: 'base'
                }) == 0) { //case insensitive checking
                    isMatch = 1;
                }
            }
            if (isMatch == 1) {
                comment = comment1;
                score += (parseInt($(".points:eq(" + x + ")").html()));
                $(".obtainedpoints:eq(" + x + ")").html($(".points:eq(" + x + ")").html() + "/");
                $('.questionnumbutton:eq('+ x +')').css("background-color","#32d264");
            } else {
                if (isshowcorrectanswers) {
                    comment = comment2;
                    $('#ans' + (x + 1)).html($('#ans' + (x + 1)).html().replace(/,,/g, ', ')); //change comma set to commas with space
                    comment = comment.concat($('#ans' + (x + 1)).html());
                } else {
                    comment = comment3;
                }
                $(".obtainedpoints:eq(" + x + ")").html("0/");
                $('.questionnumbutton:eq('+ x +')').css("background-color","#ff0808");
            }

            /* check if answered */
            if ($('[name=FITBQ' + (x + 1) + ']').val() != "") {
                questionsanswered++;
            } else {
                $('.questionnumbutton:eq('+ x +')').css("background-color","#a0a0a0");
            }
        }

        /* Essay / Constructed response */
        if ($(".questiontype:eq(" + x + ")").html() == "ESSAY") {

            /* extract the answer and display corresponding message */
            if ($(".ESSAY" + (x + 1)).val() == null || $(".ESSAY" + (x + 1)).val() == "") {
                response = "";
                partialscore = 0;
                comment = comment6;
                $(".obtainedpoints:eq(" + x + ")").html("0/");
                $('.questionnumbutton:eq('+ x +')').css("background-color","#a0a0a0");
            } else {
                response = ($(".ESSAY" + (x + 1)).val()).replace(/\n/g, '%0D%0A'); //replace new lines
                response = response.replace(/&/g, '%26'); //replace & characters
                response = response.replace(/=/g, '%3D'); //replace = characters
                response = response.replace(/\+/g, '%2B'); //replace + characters
                partialscore = -1;
                questionsanswered++;
                comment = comment5;
                $(".obtainedpoints:eq(" + x + ")").html("Not yet graded/");
                hasresponse = 1;
                if (!isCheating) {
                    $('[name="isfinal"]').val(0);
                }
            }

            /* insert response to database. Manually serialize form data */
            xhttp = new XMLHttpRequest();
            var url = "./src/insertessayaction.php";
            urlData2 = url + "?resultid=" + attemptid + "&questionid=" + $(".questionid:eq(" + x + ")").html() + "&questionnum=" + (x + 1) + "&answer=" + response + "&points=" + partialscore;
            xhttp.open("POST", urlData2, true);
            xhttp.send();
            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    res2 = JSON.parse(this.responseText);
                }
            }
        }

        comment = comment.concat("</div>");
        if (commentadded == 0) $(".question:eq(" + x + ")").append(comment);
    }
    commentadded = 1;

    //check if attempt is valid (not invalid, not over attempt limit)
    if (!isCheating) {
        if (hasresponse != 1) {
            $('#totalscore').html(score + " / " + $('#numpoints').html() + " &nbsp; (" + parseInt(score * 100 / $('#numpoints').html()) + "%)");
        } else { //indicate that score is still partial
            $('#totalscore').html(score + " / " + $('#numpoints').html() + " &nbsp; (Partial)");
            $("#essaywarning").show();
        }
        $('[name="totalscore"]').val(score);
    } else {
        $('#totalscore').html("<span style='color:red'>Invalid attempt</span>");
        $('[name="totalscore"]').val(-1);
        $("#viewquestions").hide();
    }
    $('[name="perfectscore"]').val(perfectscore);
    $('#attempttime').html(parseInt(time / 60) + " mins, " + parseInt(time % 60) + " secs");
    $('[name="attempttime"]').val(parseInt(time / 60) + " mins, " + parseInt(time % 60) + " secs");
    $('#questionsanswered').html(questionsanswered + " / " + $('#numquestions').html());

    if (isvalidattempt == 1) {
        //attempt is recordable. Action: store to database
        $('input').prop("disabled", false);
        $('select').prop("disabled", false);
        $('textarea').prop("disabled", false);
        xhttp = new XMLHttpRequest();
        var url = "./src/updateattempttoken.php";
        var data = $('#quiz').serialize();
        urlData = url + "?" + data + "&attemptid=" + attemptid;
        xhttp.open("POST", urlData, true);
        $('input').prop("disabled", true);
        $('select').prop("disabled", true);
        $('textarea').prop("disabled", true);
        xhttp.send();
        questionsanswered = 0;
        score = 0;
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                res = JSON.parse(this.responseText);
                var x = (res['status'] == 200) ? "success" : "alert";
                var y = (res['status'] == 200) ? "alert" : "success";
                $("#" + x + "content").html(res['message']);
                $('#popuperror').hide();
                $("#popup" + y).hide();
                $("#popup" + x).show();
                setTimeout(function () { $("#popup" + x).fadeOut(1000); }, (x == "success") ? 4000 : 4000);
                if (res['status'] == 200) {
                    //submission successful, proceed to reveal results
                    showtab(1, 2);
                    $('#viewresults').show();
                    $('.obtainedpoints').show();
                    $('.comment').show();
                    $(".question").show();
                    $('[id^="ans"]').remove();
                    $('#finishquiz').hide();
                    $('div.questions br').show(); //bring back hidden <br>s if displaying all items at once and came from conn error

                    if (isbacktrack != 2) {
                        //bring back question navigation buttons
                        $("#questionbuttons").show();
                        $('button#prevquestion').show();
                        $('button#nextquestion').show();
                    }
                    if (isviewquestions == 0) {
                        /* remove all question tabs to refrain user from going to dev mode and check questions there */
                        $(".question").remove();
                        $("#viewquestions").hide();
                    }
                }
            }
        };
        //situation for submission failed. Enable everything again including submit button
        //do not reveal results and comments, nor redirect to any tab
        //clear comments, reset stats
        //this would be the last set of actions since the success part is not executecd.
        $('#finishquiz').prop("disabled", false);

        $('div.questions br').hide();
        $('#finishquiz').html("<b>Quiz submitting... (Click to resubmit)</b>");
        //hide question navigation buttons
        $("#questionbuttons").hide();
        $('button#prevquestion').hide();
        $('button#nextquestion').hide();
    } else {
        //no need to submit to database. Reveal results right away
        showtab(1, 2);
        $('#viewresults').show();
        $('.obtainedpoints').show();
        $('.comment').show();
        $(".question").show();
        $('[id^="ans"]').remove();
        $('#finishquiz').hide();
        $('div.questions br').show();
        if (isviewquestions == 0 || isCheating == 1) {
            /* remove all question tabs to refrain user from going to dev mode and check questions there */
            $(".question").remove();
            $("#viewquestions").hide();
        }
    }


}

/* For exam mode (LEVEL 1 AND 2) - monitor window focus */
/* Focus mode toggle - CREDITS TO oliverbj'S CODE IN https://stackoverflow.com/questions/19519535/detect-if-browser-tab-is-active-or-user-has-switched-away */

// CHANGE TO MODAL ALERT WITH MANUAL X BUTTONS

function stopquiz(gg) {
    $('.questions').hide();
    isCheating = 1;
    //show modals
    if (gg == 1 && !$("#fullscreenerror").data('bs.modal')?.isShown) {
        $('#changetaberror').modal('show');
        $('#quiz').submit();
    }
    if (gg == 2 && !$("#changetaberror").data('bs.modal')?.isShown) {
        $('#fullscreenerror').modal('show');
        $('#quiz').submit();
    }
}

$(window).on("blur focus", function (e) {
    var prevType = $(this).data("prevType");
    e.preventDefault();
    if (prevType != e.type) {
        switch (e.type) {
            case "blur": {
                if (isexammode >= 1 && quizinprogress && !isdone) {
                    /* stop the quiz and submit blank scores */
                    stopquiz(1);
                }
            }
        }
    }

    $(this).data("prevType", e.type);
})

/* For exam mode (LEVEL 2) - monitor fullscreen focus */
var elem = document.documentElement;
function checkexammode() {
    if (isexammode == 2) {
        //disable take the quiz button and wait for user to fullscreen
        $('#takethequiz').prop("disabled", true);
        a = 0;
    } else {
        //enable take the quiz button
        $('#takethequiz').prop("disabled", false);
    }
    if (document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullScreenElement) {
        //enable take the quiz button
        $('#takethequiz').prop("disabled", false);
        a = 1;
    }
}

function togglefullscreen() {
    if (elem.requestFullscreen) {
        elem.requestFullscreen();
    } else if (elem.webkitRequestFullscreen) { /* Safari */
        elem.webkitRequestFullscreen();
        a = 1;
    } else if (elem.msRequestFullscreen) { /* IE11 */
        elem.msRequestFullscreen();
        a = 1;
    } else if (elem.mozRequestFullscreen) { /* Firefox */
        elem.mozRequestFullscreen();
        a = 1;
    }
    if (a == 1) {
        //enable take the quiz button
        $('#takethequiz').prop("disabled", false);
    }
}

/*check full screen - CODE CREDITS to https://developer.mozilla.org/en-US/docs/Web/API/Element/fullscreenchange_event
                                  and https://stackoverflow.com/questions/21461890/browser-f11-fullscreen-does-not-register-with-webkit-full-screen-or-javascript */

$(document).on('keydown', function (event) {
    if (event.which == 122) {
        event.preventDefault();
        togglefullscreen(); // From fullscreen API
    }
});

elem.addEventListener('fullscreenchange', (event) => {
    // document.fullscreenElement will point to the element that
    // is in fullscreen mode if there is one. If not, the value
    // of the property is null.
    if (document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement) {
        //enable take the quiz button
        $('#takethequiz').prop("disabled", false);
        a = 1;
    } else {
        //user is exiting full screen mode
        //toggle action according to if this occurs before or during the quiz
        if (isexammode == 2) {
            if (!quizinprogress) {
                //disable take the quiz button and wait for user to fullscreen
                $('#takethequiz').prop("disabled", true);
                a = 0;
            } else if (quizinprogress && !isdone) {
                //terminate quiz session
                stopquiz(2);
            }
        }
    }
});

/* Prevent page redirects when quiz is ongoing */
function checkbeforeredirect(myLink) {
    if (quizinprogress && !isdone) {
        //quiz in progress, do not redirect
        $('#popuperror').show();
        setTimeout(function () {
            $('#popuperror').fadeOut(1000);
        }, 2000);
    } else {
        //ok to redirect
        location.replace(myLink);
    }
}

function checksignout(e) {
    if (quizinprogress && !isdone) {
        //quiz in progress, do not redirect
        $('#popuperror').show();
        setTimeout(function () {
            $('#popuperror').fadeOut(1000);
        }, 2000);
        e.preventDefault();
    } else {
        //ok to sign out
        signoutClick(e);
    }
}
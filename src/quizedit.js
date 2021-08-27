/*----------------------------------------------------

  JS File for the quiz editing page (ANYQUIZ) v1.1
  By Wayne Dayata
  June 6, 2021

----------------------------------------------------*/

window.onload = function () {
    toggletimelimit();
    toggleattemptlimit();
    toggleviewcorrectanswers();
    toggleexammode();
    $('html').css({ "overflow-x": "hidden" });
}

function submitform(e) {
    var xhttp = new XMLHttpRequest();
    var url = "./src/updatequizaction.php";
    var data = $("#quiz").serialize();
    var urlData = url + "?" + data;
    xhttp.open("POST", urlData, true);
    xhttp.send();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            res = JSON.parse(this.responseText);
            if (res['status'] == 200) {
                $('input').prop("disabled", true);
                $('select').prop("disabled", true);
                $('textarea').prop("disabled", true);
                $('button.saveandupdate').hide();
                $('button.editagain').show();
                triggersuccess(500, 6000, "<b>Successfully updated your quiz settings!</b>");
                $('button.saveandupdate').html("<b>Save and Update</b>");
            }
        }
    }
    $('button.saveandupdate').html("<b>Updating settings... (Click to resubmit info)</b>");
    e.preventDefault();
}

function checkandupdate(e) {
    var isValid = 1;
    e.preventDefault();

    //check all inputs - Quiz name, Quiz desc, Time limit, Attempt limit
    if ($('#quizname').val() == "" || $('#quizdescription').val() == "" ||
        ($('select#istimelimit').val() == "1" && ($('input#timelimit').val() == "" || $('input#timelimit').val() <= 0 || $('input#timelimit').val() > 180 || $('input#timelimit').val() % 1 != 0)) ||
        ($('select#isattemptlimit').val() == "1" && ($('input#attemptlimit').val() == "" || $('input#attemptlimit').val() <= 0 || $('input#attemptlimit').val() > 10 || $('input#attemptlimit').val() % 1 != 0))) {
        isValid = 0;
        if ($('select#istimelimit').val() == "Enabled" && (parseInt($('input#timelimit').val()) <= 0 || parseInt($('input#timelimit').val()) > 180) || $('input#timelimit').val() % 1 != 0) {
            triggeralert(440, 3000, "Time limit must be an integer value from 1 to 180 only.");
        } else if ($('select#isattemptlimit').val() == "Enabled" && (parseInt($('input#attemptlimit').val()) <= 0 || parseInt($('input#attemptlimit').val()) > 10) || $('input#attemptlimit').val() % 1 != 0) {
            triggeralert(400, 3000, "Attempt limit must be an integer value from 1 to 10 only.");
        } else {
            triggeralert(250, 3000, "Please fill up all fields.");
        }
    }

    if (isValid) {
        submitform(e);
    }
}

function editagain() {
    $('button.editagain').prop("disabled",true);
    setTimeout(function () {
        $('input').prop("disabled", false);
        $('select').prop("disabled", false);
        $('textarea').prop("disabled", false);
        $('button.editagain').hide();
        $('button.editagain').prop("disabled",false);
        $('button.saveandupdate').show();
        toggletimelimit();
        toggleattemptlimit();
        toggleviewcorrectanswers();
    }, 300);
}

/* toggling element usability - time limit, attempt limit, elements under question type, correct answer viewing*/
function toggletimelimit() {
    if ($('select#istimelimit').val() == "-1") {
        $('input[id="timelimit"]').prop('disabled', true);
        $('input[id="timelimit"]').val("");
    } else {
        $('input[id="timelimit"]').prop('disabled', false);
    }
}

function toggleattemptlimit() {
    if ($('select#isattemptlimit').val() == "-1") {
        $('input[id="attemptlimit"]').prop('disabled', true);
        $('input[id="attemptlimit"]').val("");
    } else {
        $('input[id="attemptlimit"]').prop('disabled', false);
    }
}

function toggleviewcorrectanswers() {
    if ($('select#isviewquestions').val() == "0") {
        $('select[id="isshowcorrectanswers"]').prop('disabled', true);
        $('select[id="isshowcorrectanswers"]').val("0");
    } else {
        $('select[id="isshowcorrectanswers"]').prop('disabled', false);
    }
}

/* V1.4.3 update - new quiz mode */
function toggleexammode() {
    switch (parseInt($('#isexammode').val())) {
        case 0: {
            $('.exammodetext').html('Exam Mode Disabled - The quiz can be taken freely and no event monitoring <br>nor interruptions will occur in the session. Best for use in exercises or casual quizzes.<br>');
            break;
        }
        case 1: {
            $('.exammodetext').html('Exam Mode Level 1 - The user shall be required to stay on the page during the <br>quiz session. If a loss of tab focus is detected (i.e. switching to other tabs/windows,<br> or toggling areas away from the browser), then the session will be terminated.');
            break;
        }
        case 2: {
            $('.exammodetext').html('Exam Mode Level 2 (NEW) - The user is required to switch to <u>fullscreen mode</u><br>before and during the quiz session. If a loss of tab focus is detected or if the <br>user exits fullscreen mode, then the session will be terminated.');
            break;
        }
    }
}

/* functions for quiz info page */

function copyquizlink() {
    if (ispublic == 0) {
        //link cannot be shared since its private. Trigger alert message.
        triggeralert(500, 4000, "Cannot copy link since the quiz is made private.<br>Should you wish to make it public, please edit the quiz accessibility.")
    } else {
        //proceed to copy link
        // Code source: https://www.w3schools.com/howto/howto_js_copy_clipboard.asp  
        /* Get the text field */
        var copyText = document.getElementById('quizlink');
        /* Select the text field */
        copyText.select();
        copyText.setSelectionRange(0, 99999); /* For mobile devices */
        /* Copy the text inside the text field */
        document.execCommand("copy");
        /* Alert the copied text */
        triggersuccess(400, 4000, "<b>Successfully copied quiz link!</b>");
    }
}

function removequiz() {
    var url = "./src/deletequiz.php";
    var data = $('#deletequiz').serialize();
    var urlData = url + "?" + data;
    xhttp.open("GET", urlData, true);
    xhttp.send();
}

function deleteattempt(e, num) {
    if (confirm("Are you sure you want to delete attempt record #" + num + " by " + ($('td:eq(' + (6 * num + 1) + ')').html()) + " on " + ($('td:eq(' + (6 * num + 2) + ')').html()) + "? \n\nWARNING: This action cannot be undone!")) {
        var url = "./src/deleteattempt.php";
        var data = $('#deleteattempt' + num).serialize();
        var urlData = url + "?" + data;
        xhttp.open("GET", urlData, true);
        xhttp.send();
    } else {
        e.preventDefault();
    }
}

function deleteattempt2(e, num) {
    if (confirm("Are you sure you want to delete attempt record #" + num + " by " + ($('td:eq(' + (6 * num + 2) + ')').html()) + " on " + ($('td:eq(' + (6 * num + 3) + ')').html()) + "? \n\nWARNING: This action cannot be undone!")) {
        var url = "./src/deleteattempt.php";
        var data = $('#deleteattempt' + num).serialize();
        var urlData = url + "?" + data;
        xhttp.open("GET", urlData, true);
        xhttp.send();
    } else {
        e.preventDefault();
    }
}


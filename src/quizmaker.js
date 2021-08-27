/*----------------------------------------------------

  JS File for the quiz making page (ANYQUIZ) v1.1
  By Wayne Dayata
  June 5, 2021

----------------------------------------------------*/


/* toggle show/hide of tabs - main tabs (quiz settings, question tabs, end screen) */
function showtab(fromtab, totab) {
    //check if all inputs are filled
    var isValid = 1;
    var title;
    if (fromtab == 0) {
        if ($('#quizname').val() == "" || $('#quizdescription').val() == "" ||
            ($('select#istimelimit').val() == "Enabled" && ($('input#timelimit').val() == "" || $('input#timelimit').val() <= 0 || $('input#timelimit').val() > 180 || $('input#timelimit').val() % 1 != 0)) ||
            ($('select#isattemptlimit').val() == "Enabled" && ($('input#attemptlimit').val() == "" || $('input#attemptlimit').val() <= 0 || $('input#attemptlimit').val() > 10 || $('input#attemptlimit').val() % 1 != 0)) ||
            !(parseInt($('#isbacktrack').val()) >= 0) || $('#isshufflequestionorder').val() == "" || !(parseInt($('#isexammode').val()) >= 0)) {
            isValid = 0;
            if ($('select#istimelimit').val() == "Enabled" && (parseInt($('input#timelimit').val()) <= 0 || parseInt($('input#timelimit').val()) > 180) || $('input#timelimit').val() % 1 != 0) {
                triggeralert("Time limit must be an integer value from 1 to 180 only.");
            } else if ($('select#isattemptlimit').val() == "Enabled" && (parseInt($('input#attemptlimit').val()) <= 0 || parseInt($('input#attemptlimit').val()) > 10) || $('input#attemptlimit').val() % 1 != 0) {
                triggeralert("Attempt limit must be an integer value from 1 to 10 only.");
            } else {
                triggeralert("Please fill up or select all fields.");
            }
        }
    }
    if (fromtab == 1 && totab != 0) {
        if (!checkquestion()) isValid = 0;
    }
    if (totab == 2) {
        countquestions();
        countpoints();
        generateCode();

        //check for essay questions
        if ($('#hasessay').val() == "1") {
            //show the alert info message for essay
            $("#essaywarning").show();
            if ($('#isshufflequestionorder').val() == "Yes") {
                //show the essayselect dropdown
                $("#essayselect").show();
            } else {
                $("#essayselect").hide();
                $("#showessays").val("2");
            }
        } else {
            $("#essaywarning").hide();
            $("#essayselect").hide();
            $("#showessays").val("2");
        }
    }
    if (isValid) {
        title = $("#quizname").val();
        $(".maintab").hide();
        $(".maintab:eq(" + totab + ")").show();
        $(".quizTitle").html(title);
        $("#quiztitle2").html(title);
        $('.createquestion').html("<b>View questions</b>");
    }
}

/* toggle show/hide of tabs - question tabs (Q 1 to N)*/
var visibleDiv = 0; /* current question number (0 to N-1) */

function showDiv() {
    $(".question").hide();
    $(".question:eq(" + visibleDiv + ")").show();
    checkCurrTab();
}
showDiv()

function showNext() {
    if (checkquestion()) {
        if (visibleDiv != $(".question").length - 1) {
            visibleDiv++;
        }
        showDiv();
    }
}


function showPrev() {
    if (checkquestion()) {
        if (visibleDiv != 0) {
            visibleDiv--;
        }
        showDiv();
    }
}

function showquestion(x) {
    if (checkquestion()) {
        visibleDiv = x - 1;
        showDiv();
    }
}

function checkCurrTab() {
    if (visibleDiv == $(".question").length - 1) {
        $('button#nextquestion').hide();
        $('button#removequestion').show();
    } else {
        $('button#nextquestion').show();
        $('button#removequestion').hide();
    }

    if (visibleDiv == 0) {
        $('button#prevquestion').hide();
    } else {
        $('button#prevquestion').show();
    }

    if ($(".question").length == 1) {
        $('button#removequestion').hide();
    }
}

/* toggling element usability - time limit, attempt limit, elements under question type, correct answer viewing*/
function toggletimelimit() {
    if ($('select#istimelimit').val() == "Disabled") {
        $('input[id="timelimit"]').prop('disabled', true);
        $('input[id="timelimit"]').val("");
    } else {
        $('input[id="timelimit"]').prop('disabled', false);
    }
}

function toggleattemptlimit() {
    if ($('select#isattemptlimit').val() == "Disabled") {
        $('input[id="attemptlimit"]').prop('disabled', true);
        $('input[id="attemptlimit"]').val("");
    } else {
        $('input[id="attemptlimit"]').prop('disabled', false);
    }
}

function toggleviewcorrectanswers() {
    if ($('select#isviewquestions').val() == "Disabled") {
        $('select[id="isshowcorrectanswers"]').prop('disabled', true);
        $('select[id="isshowcorrectanswers"]').val("Disabled");
    } else {
        $('select[id="isshowcorrectanswers"]').prop('disabled', false);
        $('select[id="isshowcorrectanswers"]').val("");
    }
}

/* V1.4.3 update - new quiz mode */
function toggleexammode() {
    switch (parseInt($('#isexammode').val())) {
        case 0: {
            $('.exammodetext').html('Exam Mode Disabled - The quiz can be taken freely and no event monitoring nor interruptions will occur in the session. Best for use in exercises or casual quizzes.');
            break;
        }
        case 1: {
            $('.exammodetext').html('Exam Mode Level 1 - The user shall be required to stay on the page during the quiz session. If a loss of tab focus is detected (i.e. switching to other tabs/windows, or toggling areas away from the browser), then the session will be terminated.');
            break;
        }
        case 2: {
            $('.exammodetext').html('Exam Mode Level 2 (NEW) - The user is required to switch to <u>fullscreen mode</u> before and during the quiz session. If a loss of tab focus is detected or if the user exits fullscreen mode, then the session will be terminated.');
            break;
        }
        default: {
            $('.exammodetext').html('Select an exam mode setting to view its description.');
            break;
        }
    }
}

/* toggle visibility of question type content */
function togglequestiontype() {
    if ($("select.questiontype:eq(" + visibleDiv + ")").val() == "TF") {
        $(".questiondescription:eq(" + visibleDiv + ")").css("height","116px");
        $(".MTF:eq(" + visibleDiv + ")").hide();
        $(".MCQ:eq(" + visibleDiv + ")").hide();
        $(".MCMAQ:eq(" + visibleDiv + ")").hide();
        $(".FITBQ:eq(" + visibleDiv + ")").hide();
        $(".ESSAY:eq(" + visibleDiv + ")").hide();
        $(".TF:eq(" + visibleDiv + ")").show(300);
    }
    if ($("select.questiontype:eq(" + visibleDiv + ")").val() == "MTF") {
        if ($(".questiondescription:eq(" + visibleDiv + ")").val() == "") 
            $(".questiondescription:eq(" + visibleDiv + ")").val("Read and analyze the statements below, then select the correct option.");
        $(".questiondescription:eq(" + visibleDiv + ")").css("height","55px");
        $(".TF:eq(" + visibleDiv + ")").hide();
        $(".MCQ:eq(" + visibleDiv + ")").hide();
        $(".MCMAQ:eq(" + visibleDiv + ")").hide();
        $(".FITBQ:eq(" + visibleDiv + ")").hide();
        $(".ESSAY:eq(" + visibleDiv + ")").hide();
        $(".MTF:eq(" + visibleDiv + ")").show(300);
    }
    if ($("select.questiontype:eq(" + visibleDiv + ")").val() == "MCQ") {
        $(".questiondescription:eq(" + visibleDiv + ")").css("height","116px");
        $(".TF:eq(" + visibleDiv + ")").hide();
        $(".MTF:eq(" + visibleDiv + ")").hide();
        $(".MCMAQ:eq(" + visibleDiv + ")").hide();
        $(".FITBQ:eq(" + visibleDiv + ")").hide();
        $(".ESSAY:eq(" + visibleDiv + ")").hide();
        $(".MCQ:eq(" + visibleDiv + ")").show(300);
    }
    if ($("select.questiontype:eq(" + visibleDiv + ")").val() == "MCMAQ") {
        $(".questiondescription:eq(" + visibleDiv + ")").css("height","116px");
        $(".MCQ:eq(" + visibleDiv + ")").hide();
        $(".TF:eq(" + visibleDiv + ")").hide();
        $(".MTF:eq(" + visibleDiv + ")").hide();
        $(".FITBQ:eq(" + visibleDiv + ")").hide();
        $(".ESSAY:eq(" + visibleDiv + ")").hide();
        $(".MCMAQ:eq(" + visibleDiv + ")").show(300);
    }
    if ($("select.questiontype:eq(" + visibleDiv + ")").val() == "FITBQ") {
        $(".questiondescription:eq(" + visibleDiv + ")").css("height","116px");
        $(".MCQ:eq(" + visibleDiv + ")").hide();
        $(".MCMAQ:eq(" + visibleDiv + ")").hide();
        $(".TF:eq(" + visibleDiv + ")").hide();
        $(".MTF:eq(" + visibleDiv + ")").hide();
        $(".ESSAY:eq(" + visibleDiv + ")").hide();
        $(".FITBQ:eq(" + visibleDiv + ")").show(300);
    }
    if ($("select.questiontype:eq(" + visibleDiv + ")").val() == "ESSAY") {
        $(".questiondescription:eq(" + visibleDiv + ")").css("height","116px");
        $(".MCQ:eq(" + visibleDiv + ")").hide();
        $(".MCMAQ:eq(" + visibleDiv + ")").hide();
        $(".FITBQ:eq(" + visibleDiv + ")").hide();
        $(".TF:eq(" + visibleDiv + ")").hide();
        $(".MTF:eq(" + visibleDiv + ")").hide();
        $(".ESSAY:eq(" + visibleDiv + ")").show(300);
    }
    if ($("select.questiontype:eq(" + visibleDiv + ")").val() == "") {
        $(".questiondescription:eq(" + visibleDiv + ")").css("height","116px");
        $(".MCQ:eq(" + visibleDiv + ")").hide(300);
        $(".MCMAQ:eq(" + visibleDiv + ")").hide(300);
        $(".FITBQ:eq(" + visibleDiv + ")").hide(300);
        $(".ESSAY:eq(" + visibleDiv + ")").hide(300);
        $(".TF:eq(" + visibleDiv + ")").hide(300);
        $(".MTF:eq(" + visibleDiv + ")").hide(300);
    }
}

function toggleMCQ(x) {
    if ($("input.MCQ" + x + ":eq(" + visibleDiv + ")").val() != "") {
        $("input#MCQCorr" + visibleDiv + "-" + x).prop('disabled', false);
    } else {
        $("input#MCQCorr" + visibleDiv + "-" + x).prop('disabled', true);
        $("input#MCQCorr" + visibleDiv + "-" + x).prop('checked', false);
    }
}

function addFITBA() {
    var content = '<br><input name="FITB' + visibleDiv + '[]" class="form-control FITB' + visibleDiv + '" style="display:inline; width:500px;" placeholder="Input answer text">';
    if ($('.FITB' + visibleDiv).length == 9) {
        $('.addFITBA:eq(' + visibleDiv + ')').hide();
    }
    $('.FITB' + visibleDiv + ":last").after(content);
    $('.removeFITBA:eq(' + visibleDiv + ')').show();
}

function removeFITBA() {
    $('.FITB' + visibleDiv + ":last").remove();
    $('.FITB' + visibleDiv + ':last+br').remove();
    if ($('.FITB' + visibleDiv).length == 1) {
        $('.removeFITBA:eq(' + visibleDiv + ')').hide();
    }
    $('.addFITBA:eq(' + visibleDiv + ')').show();
}

function toggleMCMAQ(x) {
    if ($("input.MCMAQ" + visibleDiv + "-" + x).val() != "") {
        $("input#MCMAQCorr" + visibleDiv + "-" + x).prop('disabled', false);
    } else {
        $("input#MCMAQCorr" + visibleDiv + "-" + x).prop('disabled', true);
        $("input#MCMAQCorr" + visibleDiv + "-" + x).prop('checked', false);
    }
}

function addMCMAA() {
    var content = '<div class="MAQ' + visibleDiv + '">Option ' + ($('.MAQ' + visibleDiv).length + 1) + ': <input name="MCMAQ' + visibleDiv + '[]" class="form-control MCMAQ' + visibleDiv + '-' + ($('.MAQ' + visibleDiv).length + 1) + '" style="display:inline; width:500px;" placeholder="Option ' + ($('.MAQ' + visibleDiv).length + 1) + '" onkeyup="toggleMCMAQ(' + ($('.MAQ' + visibleDiv).length + 1) + ')"> &nbsp; <label><input type="checkbox" id="MCMAQCorr' + visibleDiv + '-' + ($('.MAQ' + visibleDiv).length + 1) + '" name="MCMAQcorr' + visibleDiv + '-' + ($('.MAQ' + visibleDiv).length + 1) + '" value="1" disabled> &nbsp; Correct answer</label></div>';
    if ($('.MAQ' + visibleDiv).length == 8) {
        $('.addMCMAA:eq(' + visibleDiv + ')').hide();
    }
    $('.MAQ' + visibleDiv + ":last").after(content);
    $('.removeMCMAA:eq(' + visibleDiv + ')').show();
}

function removeMCMAA() {
    $('.MAQ' + visibleDiv + ":last").remove();
    if ($('.MAQ' + visibleDiv).length == 3) {
        $('.removeMCMAA:eq(' + visibleDiv + ')').hide();
    }
    $('.addMCMAA:eq(' + visibleDiv + ')').show();
}

function addMTFstatement() {
    $('.MTFstatement3:eq(' + visibleDiv + ')').show();
    $('.addstatement:eq(' + visibleDiv + ')').hide();
    $('.removestatement:eq(' + visibleDiv + ')').show();
    $('input[name=numstatements' + visibleDiv + ']').val("3");
}

function removeMTFstatement() {
    $('.MTFstatement3:eq(' + visibleDiv + ')').hide();
    $('.addstatement:eq(' + visibleDiv + ')').show();
    $('.removestatement:eq(' + visibleDiv + ')').hide();
    $('input[name=numstatements' + visibleDiv + ']').val("2");
    $('input.MTF' + visibleDiv + '-3').val("");
    $("input[name=MTFcorr" + visibleDiv + "-3").prop('checked', false);
}

function togglesharelink() {
    if ($('select#accessibility').val() == "Enabled") {
        $('.quizlink').show(250);
    } else {
        $('.quizlink').hide(250);
    }
}

function copyquizlink() {
    // Code source: https://www.w3schools.com/howto/howto_js_copy_clipboard.asp  
    /* Get the text field */
    var copyText = document.getElementById('quizlink');
    /* Select the text field */
    copyText.select();
    copyText.setSelectionRange(0, 99999); /* For mobile devices */
    /* Copy the text inside the text field */
    document.execCommand("copy");
    /* Alert the copied text */
    triggersuccess("<b>Successfully copied quiz link!</b>");
}

/* add and remove questions*/
function addQuestion() {
    if (checkquestion()) {
        visibleDiv = $('.question').length;
        var content1 = `
            <div class="question">
                 <div class="row">
                     <div class="count questionnum" style="padding-left: 35px;">Question ` + (($('.question').length) + 1) + `</div>
                 </div>
                 <div class="count-7" style="padding-left: 60px; font-size: 18px;">Question Type:
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
                            <textarea name="questiondescription[]" placeholder="Question text (required) - Drag the lower right corner of the text area to toggle its size" class="questiondescription form-control" style="width:480px; height:116px; min-height: 35px; min-width:320px; max-width:650px; max-height:177px;"></textarea>
                        </div>
                    </div>
                    <div class="TF" style="padding-left: 2px; font-size: 18px; padding-top: 20px; display:none;">
                        Select correct answer: &nbsp; &nbsp; &nbsp;
                        <input type="radio" id="TFCorr` + ($('.question').length) + `-1" name="TFcorr` + ($('.question').length) + `" value="TRUE"><label for="TFCorr` + ($('.question').length) + `-1"> &nbsp; TRUE</label>  &nbsp; &nbsp; &nbsp; 
                        <input type="radio" id="TFCorr` + ($('.question').length) + `-2" name="TFcorr` + ($('.question').length) + `" value="FALSE"><label for="TFCorr` + ($('.question').length) + `-2"> &nbsp; FALSE</label><br>
                    </div>
                    <div class="MTF" style="padding-left: 2px; font-size: 18px; padding-top: 20px; display:none; max-width:750px;">
                        Input statements and their states: <br>
                        <input name="MTF` + ($('.question').length) + `-1" class="form-control MTF` + ($('.question').length) + `-1" style="display:inline; width:500px;" placeholder="Input first statement (required)">  &nbsp;
                        <input type="radio" id="MTFCorr` + ($('.question').length) + `-1" name="MTFcorr` + ($('.question').length) + `-1" value="TRUE"><label for="MTFCorr` + ($('.question').length) + `-1"> &nbsp; TRUE</label> &nbsp; &nbsp;
                        <input type="radio" id="MTFCorr` + ($('.question').length) + `-2" name="MTFcorr` + ($('.question').length) + `-1" value="FALSE"><label for="MTFCorr` + ($('.question').length) + `-2"> &nbsp; FALSE</label>
                        <br>
                        <input name="MTF` + ($('.question').length) + `-2" class="form-control MTF` + ($('.question').length) + `-2" style="display:inline; width:500px;" placeholder="Input second statement (required)">  &nbsp;
                        <input type="radio" id="MTFCorr` + ($('.question').length) + `-3" name="MTFcorr` + ($('.question').length) + `-2" value="TRUE"><label for="MTFCorr` + ($('.question').length) + `-3"> &nbsp; TRUE</label> &nbsp; &nbsp;
                        <input type="radio" id="MTFCorr` + ($('.question').length) + `-4" name="MTFcorr` + ($('.question').length) + `-2" value="FALSE"><label for="MTFCorr` + ($('.question').length) + `-4"> &nbsp; FALSE</label>
                        <br>
                        <div class="MTFstatement3" style="display:none">
                            <input name="MTF` + ($('.question').length) + `-3" class="form-control MTF` + ($('.question').length) + `-3" style="display:inline; width:500px;" placeholder="Input third statement (required)">  &nbsp;
                            <input type="radio" id="MTFCorr` + ($('.question').length) + `-5" name="MTFcorr` + ($('.question').length) + `-3" value="TRUE"><label for="MTFCorr` + ($('.question').length) + `-5"> &nbsp; TRUE</label> &nbsp; &nbsp;
                            <input type="radio" id="MTFCorr` + ($('.question').length) + `-6" name="MTFcorr` + ($('.question').length) + `-3" value="FALSE"><label for="MTFCorr` + ($('.question').length) + `-6"> &nbsp; FALSE</label>
                            <br>
                        </div>
                        <button type="button" class="addstatement btn btn-info" onclick="addMTFstatement()">Add another statement</button>
                        <button type="button" class="removestatement btn btn-danger" onclick="removeMTFstatement()" style="display:none;">Remove statement</button><br>
                        <br>
                        Order of statements entered above will be shuffled during the quiz. <br>
                        Options will be provided for the quiz takers to specifically identify which among the statemnents are true and false.
                        <input type="hidden" name="numstatements` + ($('.question').length) + `" value="2">
                    </div>
                    <div class="MCQ" style="padding-left: 2px; font-size: 18px; padding-top: 20px; display:none;">
                        Input option texts: (First two fields are required)<br>
                        Option 1: <input name="MCQ1[]" class="form-control MCQ1" style="display:inline; width:500px;" placeholder="Option 1 (required)" onkeyup="toggleMCQ(1)"> &nbsp; <input type="radio" id="MCQCorr` + ($('.question').length) + `-1" name="MCQcorr` + ($('.question').length) + `" value="1" disabled><label for="MCQCorr` + ($('.question').length) + `-1"> &nbsp; Correct answer</label><br>
                        Option 2: <input name="MCQ2[]" class="form-control MCQ2" style="display:inline; width:500px;" placeholder="Option 2 (required)" onkeyup="toggleMCQ(2)"> &nbsp; <input type="radio" id="MCQCorr` + ($('.question').length) + `-2" name="MCQcorr` + ($('.question').length) + `" value="2" disabled><label for="MCQCorr` + ($('.question').length) + `-2"> &nbsp; Correct answer</label><br>
                        Option 3: <input name="MCQ3[]" class="form-control MCQ3" style="display:inline; width:500px;" placeholder="Option 3" onkeyup="toggleMCQ(3)"> &nbsp; <input type="radio" id="MCQCorr` + ($('.question').length) + `-3" name="MCQcorr` + ($('.question').length) + `" value="3" disabled><label for="MCQCorr` + ($('.question').length) + `-3"> &nbsp; Correct answer</label><br>
                        Option 4: <input name="MCQ4[]" class="form-control MCQ4" style="display:inline; width:500px;" placeholder="Option 4" onkeyup="toggleMCQ(4)"> &nbsp; <input type="radio" id="MCQCorr` + ($('.question').length) + `-4" name="MCQcorr` + ($('.question').length) + `" value="4" disabled><label for="MCQCorr` + ($('.question').length) + `-4"> &nbsp; Correct answer</label> <br>
                        Option 5: <input name="MCQ5[]" class="form-control MCQ5" style="display:inline; width:500px;" placeholder="Option 5" onkeyup="toggleMCQ(5)"> &nbsp; <input type="radio" id="MCQCorr` + ($('.question').length) + `-5" name="MCQcorr` + ($('.question').length) + `" value="5" disabled><label for="MCQCorr` + ($('.question').length) + `-5"> &nbsp; Correct answer</label> <br>
                        Option order will be shuffled during quiz.
                    </div>
                    <div class="MCMAQ" style="padding-left: 2px; font-size: 18px; padding-top: 20px; display:none;">
                        Input option texts: (First three fields are required)<br>
                        <div class="MAQ` + ($('.question').length) + `">Option 1: <input name="MCMAQ` + ($('.question').length) + `[]" class="form-control MCMAQ` + visibleDiv + `-1" style="display:inline; width:500px;" placeholder="Option 1 (required)" onkeyup="toggleMCMAQ(1)"> &nbsp; <label><input type="checkbox" id="MCMAQCorr` + ($('.question').length) + `-1" name="MCMAQcorr` + ($('.question').length) + `-1" value="1" disabled> &nbsp; Correct answer</label></div>
                        <div class="MAQ` + ($('.question').length) + `">Option 2: <input name="MCMAQ` + ($('.question').length) + `[]" class="form-control MCMAQ` + visibleDiv + `-2" style="display:inline; width:500px;" placeholder="Option 2 (required)" onkeyup="toggleMCMAQ(2)"> &nbsp; <label><input type="checkbox" id="MCMAQCorr` + ($('.question').length) + `-2" name="MCMAQcorr` + ($('.question').length) + `-2" value="1" disabled> &nbsp; Correct answer</label></div>
                        <div class="MAQ` + ($('.question').length) + `">Option 3: <input name="MCMAQ` + ($('.question').length) + `[]" class="form-control MCMAQ` + visibleDiv + `-3" style="display:inline; width:500px;" placeholder="Option 3 (required)" onkeyup="toggleMCMAQ(3)"> &nbsp; <label><input type="checkbox" id="MCMAQCorr` + ($('.question').length) + `-3" name="MCMAQcorr` + ($('.question').length) + `-3" value="1" disabled> &nbsp; Correct answer</label></div>
                        <div class="MAQ` + ($('.question').length) + `">Option 4: <input name="MCMAQ` + ($('.question').length) + `[]" class="form-control MCMAQ` + visibleDiv + `-4" style="display:inline; width:500px;" placeholder="Option 4" onkeyup="toggleMCMAQ(4)"> &nbsp; <label><input type="checkbox" id="MCMAQCorr` + ($('.question').length) + `-4" name="MCMAQcorr` + ($('.question').length) + `-4" value="1" disabled> &nbsp; Correct answer</label></div>
                        <div class="MAQ` + ($('.question').length) + `">Option 5: <input name="MCMAQ` + ($('.question').length) + `[]" class="form-control MCMAQ` + visibleDiv + `-5" style="display:inline; width:500px;" placeholder="Option 5" onkeyup="toggleMCMAQ(5)"> &nbsp; <label><input type="checkbox" id="MCMAQCorr` + ($('.question').length) + `-5" name="MCMAQcorr` + ($('.question').length) + `-5" value="1" disabled> &nbsp; Correct answer</label></div>
                        <button type="button" class="addMCMAA btn btn-info" onclick="addMCMAA()">Add another answer</button>
                        <button type="button" class="removeMCMAA btn btn-danger" onclick="removeMCMAA()">Remove answer</button><br>
                        Option order will be shuffled during quiz.
                    </div>
                    <div class="FITBQ" style="padding-left: 2px; font-size: 18px; padding-top: 20px; display:none;">
                         Input accepted answer(s):<br>
                        <input name="FITB` + ($('.question').length) + `[]" class="form-control FITB` + ($('.question').length) + `" style="display:inline; width:500px;" placeholder="Input answer text (required)"><br>
                        <button type="button" class="addFITBA btn btn-info" onclick="addFITBA()">Add another answer</button>
                        <button type="button" class="removeFITBA btn btn-danger" onclick="removeFITBA()" style="display:none;">Remove answer</button><br>
                        Answers are neither case-sensitive nor space-sensitive.
                    </div>
                    <div class="ESSAY" style="padding-left: 2px; font-size: 18px; padding-top: 20px; display:none;">
                        Responses from quiz takers will be recorded and are to be manually scored after the quiz.<br>
                    </div>
                </div>
            </div>
        `;
        var content2 = `
            <span class="space"> &nbsp; </span><button type="button" class="questionnumbutton" style="background-color:dodgerblue; color: white; margin-bottom:10px; border-radius:5px;" onclick="showquestion(` + ($('.question').length + 1) + `)">` + ($('.question').length + 1) + `</button>
        `;
        $('.questions').append(content1);
        $('#questionbuttons').append(content2);
        showDiv();
    }
}

function removeQuestion() {
    if (confirm("Are you sure you want to remove this question?")) {
        $(".question:eq(" + visibleDiv + ")").remove();
        $(".questionnumbutton:eq(" + visibleDiv + ")").remove();
        $(".space:eq(" + visibleDiv + ")").remove();
        visibleDiv--;
        showDiv();
    }
}

/* check inputs in questions */
function checkquestion() {
    var isValid = 1, x, numboxeschecked, reason;
    /* check type, desc, and points value */
    if ($('.questiontype:eq(' + visibleDiv + ')').val() == "" || $('.points:eq(' + visibleDiv + ')').val() == "" || $('.points:eq(' + visibleDiv + ')').val() <= 0 || parseInt($('.points:eq(' + visibleDiv + ')').val()) > 100 || $('.points:eq(' + visibleDiv + ')').val() % 1 != 0 || $('.questiondescription:eq(' + visibleDiv + ')').val() == "") {
        isValid = 0;
    }
    if (isValid) {
        /* check T/F - if radio button checked */
        if ($('.questiontype:eq(' + visibleDiv + ')').val() == "TF" && (!$("input[name='TFcorr" + visibleDiv + "']:checked").val())) {
            isValid = 0;
            reason = "TFnocorrectanswer";
        }
        /* check MTF - if statements are applied and if radio buttons are checked */
        if ($('.questiontype:eq(' + visibleDiv + ')').val() == "MTF") {
            //check if statements 1-2 are populated and if there are options
            if ($('.MTF' + visibleDiv + '-1').val() == "" || $('.MTF' + visibleDiv + '-2').val() == "") {
                isValid = 0;
                reason = "MTFmissingstatement";
            } else if ((!$("input[name='MTFcorr" + visibleDiv + "-1']:checked").val()) || (!$("input[name='MTFcorr" + visibleDiv + "-2']:checked").val())) {
                isValid = 0;
                reason = "MTFnocorrectanswer";
            } else if ($('input[name=numstatements' + visibleDiv + ']').val() == "3") {
                if ($('.MTF' + visibleDiv + '-3').val() == "") {
                    isValid = 0;
                    reason = "MTFmissingstatement";
                } else if (!$("input[name='MTFcorr" + visibleDiv + "-3']:checked").val()) {
                    isValid = 0;
                    reason = "MTFnocorrectanswer";
                }
            }
        }

        /* check MCQ - if radio button checked and options 1 and 2 have values*/
        if ($('.questiontype:eq(' + visibleDiv + ')').val() == "MCQ") {
            if (($('.MCQ0:eq(' + visibleDiv + ')').val() == "" || $('.MCQ1:eq(' + visibleDiv + ')').val() == "")) {
                isValid = 0;
                reason = "MCQfirst2optionsmissing";
            } else if (!($("input[name='MCQcorr" + visibleDiv + "']:checked").val())) {
                isValid = 0;
                reason = "MCQnocorrectanswer";
            }
        }
        /* check MCMAQ - if options 1 - 3 have values and min 2 checkboxes checked*/
        if ($('.questiontype:eq(' + visibleDiv + ')').val() == "MCMAQ") {
            //check options 1-3 if populated
            if ($('.MCMAQ' + visibleDiv + '-1').val() == "" || $('.MCMAQ' + visibleDiv + '-2').val() == "" || $('.MCMAQ' + visibleDiv + '-3').val() == "") {
                isValid = 0;
                reason = "MCMAQfirst3optionsmissing";
            } else {
                numboxeschecked = 0;
                //check if min 2 boxes checked
                for (x = 1; x < 9; x++) {
                    if (!(!($("input[name='MCMAQcorr" + visibleDiv + "-" + x + "']:checked").val()))) { //!(!(undefined)) == false
                        numboxeschecked++;
                    }
                }
                if (numboxeschecked < 2) {
                    isValid = 0;
                    reason = "MCMAQnotenoughanswers";
                }
            }
        }
        /* check FITBQ - if first answer filled*/
        if ($('.questiontype:eq(' + visibleDiv + ')').val() == "FITBQ" && $('.FITB' + visibleDiv + ':eq(0)').val() == "") {
            isValid = 0;
            reason = "FITBQnocorrectanswer";
        }
    }

    if (isValid == 0) { //display corresponding error messages
        if ($('.questiontype:eq(' + visibleDiv + ')').val() == "") {
            triggeralert("Please select a question type.");
        } else if ($('.points:eq(' + visibleDiv + ')').val() % 1 != 0 || parseInt($('.points:eq(' + visibleDiv + ')').val()) <= 0 || parseInt($('.points:eq(' + visibleDiv + ')').val()) > 100) {
            triggeralert("Number of points must be an integer from 1 to 100 only.");
        } else if (reason == "TFnocorrectanswer" || reason == "MCQnocorrectanswer") {
            triggeralert("Please select a correct answer.");
        } else if (reason == "MCQfirst2optionsmissing") {
            triggeralert("First two option fields are required.");
        } else if (reason == "MCMAQfirst3optionsmissing") {
            triggeralert("First three option fields are required.");
        } else if (reason == "MCMAQnotenoughanswers") {
            triggeralert("At least two correct answers are required.");
        } else if (reason == "MTFmissingstatement") {
            triggeralert("Please fill in the required statements.");
        } else if (reason == "MTFnocorrectanswer") {
            triggeralert("Please select the state of all the statements.");
        } else if (reason == "FITBQnocorrectanswer") {
            triggeralert("Please input a correct answer.");
        } else {
            triggeralert("Please fill up or select all required fields.");
        }
    }

    return isValid;
}

function countquestions() {
    var result = $('.question').length;
    $('#numquestions').html(result);
    //check if there are essay questions
    $("#hasessay").val("0");
    for (var x = 0; x < result; x++) {
        if ($("select.questiontype:eq(" + x + ")").val() == "ESSAY") {
            $("#hasessay").val("1");
        }
    }
}

function countpoints() {
    var result = 0, x;
    for (x = 0; x < $('.question').length; x++) {
        result += parseInt($('.points:eq(' + x + ')').val());
    }
    $('#numpoints').html(result);
}

function generateCode() {
    var result, characters, charactersLength, isunique = 0;
    while (isunique == 0) {
        result = [];
        characters = 'ABCDEFGHIJKLMNOPQRSTUVXYZ0123456789';
        charactersLength = characters.length;
        for (var i = 0; i < 6; i++) {
            result.push(characters.charAt(Math.floor(Math.random() *
                charactersLength)));
        }
        result = result.join('');

        //check if quiz code is unique by checking with database
        var url = "./src/checkquizcode.php";
        var urlData = url + "?quizcode=" + result;
        xhttp.open("POST", urlData, false);
        xhttp.send();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                res = JSON.parse(this.responseText);
                if (res['data'] == "ok") {
                    isunique = 1;
                }
            }
        };
    }

    $('#quizcode1').html(result);
    $('#quizcode2').val(result);
    $('#quizlink').val("https://anyquiz.me/quiztaking.php?quizcode=" + result);
}

var issubmit = 0;
function submitform() {
    issubmit = 1;
    $('#quiz').submit();
}

/* pop up alert messages */
function triggeralert(myMessage) {
    $("#alertcontent").html(myMessage);
    $("#popupsuccess").hide();
    $("#popupalert").show();
    setTimeout(function () { $("#popupalert").fadeOut(1000); }, 3000);
}

function triggersuccess(myMessage) {
    $("#successcontent").html(myMessage);
    $("#popupalert").hide();
    $("#popupsuccess").show();
    setTimeout(function () { $("#popupsuccess").fadeOut(1000); }, 3000);
}

function submitquiz(e) {
    e.preventDefault();
    var xhttp = new XMLHttpRequest();
    var url = "./src/insertquizaction.php";
    var data = $('#quiz').serialize();
    urlData = url + "?" + data;
    xhttp.open("POST", urlData, true);
    xhttp.send();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            res = JSON.parse(this.responseText);
            if (res['status'] == 200) {
                location.replace("quizsuccess.php");
            }
        }
    };
    //default (if submission failed)
    $('.publish').html("<b>Quiz submitting... (Click to resubmit)</b>");

}

//Confirm to exit site
window.onbeforeunload = function () {
    if (issubmit == 0) return "";
};
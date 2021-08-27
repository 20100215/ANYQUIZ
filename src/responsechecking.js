/*----------------------------------------------------

  JS File for the response checking page (ANYQUIZ) v1.9
  By Wayne Dayata
  July 4, 2021

----------------------------------------------------*/

var xhttp;
var isValid;
var url;
var data;
var urlData;
var success;
var count;
var unscoredresponses;

function checkFeedback(e) {
    e.preventDefault();
    isValid = true;
    unscoredresponses = 0;
    /* check if the form is properly filled - points */
    for (count = 1; count <= $('#numessays').val(); count++) {
        if (parseFloat($('#points' + count).val()) < 0) {
            isValid = false;
            triggeralert(400, 3500, "For question " + count + ", points must not be a negative value.");
            break;
        } else if (parseFloat($('#points' + count).val()) > parseFloat($('#maxpoints' + count).val())) {
            isValid = false;
            triggeralert(400, 3500, "For question " + count + ", points must be less than the maximum value indicated.");
            break;
        } else if ($('#points' + count).val() != "" && parseFloat($('#points' + count).val()) % 0.5 != 0) {
            isValid = false;
            triggeralert(400, 3500, "For question " + count + ", points must be integral or a multiple of 0.5.");
            break;
        } else if ($('#points' + count).val() == ""){
            unscoredresponses++;
        }
    }

    /* if valid - update the database with the new scores and disable inputs */
    /* otherwise, display error message */
    if (isValid) {
        if (unscoredresponses > 0){
            if (!(confirm("You have "+unscoredresponses+" unscored responses. Confirm to proceed without establishing a final score for the quiz taker?"))){
                isValid = false;
            }
        }
    }

    if (isValid) {
        xhttp = new XMLHttpRequest();
        url = "./src/updateessayaction.php";
        data = $("#quiz").serialize();
        urlData = url + "?" + data;
        xhttp.open("POST", urlData, true);
        xhttp.send();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                res = JSON.parse(this.responseText);
                if (res['status'] == 200) {
                    //disable inputs
                    $('.toggle').prop("disabled", true);
                    //show edit again button
                    $('button.saveandupdate').hide();
                    $('button.editagain').show();
                    $('button.saveandupdate').html("<b>Save and Update</b>");
                    //update score display
                    $('#partialpoints').val(res['newpartialpoints']);
                    $('#currentscore').html(parseFloat($('#currentscore').html()) + res['diff']);
                    //trigger success message
                    triggersuccess(500, 6000, "<b>Successfully updated quiz scores and feedback!</b>");
                }
            }
        }
    }
}

function togglefeedback(x) {
    if ($('.togglefeedback' + x).is(":checked")) {
        $('.feedbackbox' + x).slideDown(300);
    } else {
        $('.feedbackbox' + x).slideUp(300);
        setInterval(function () { $('.feedback' + x).val("") }, 300);
    }
}

function editagain() {
    $('button.editagain').prop("disabled", true);
    setTimeout(function () {
        $('.toggle').prop("disabled", false);
        $('button.editagain').hide();
        $('button.editagain').prop("disabled", false);
        $('button.saveandupdate').show();
    }, 400);
}

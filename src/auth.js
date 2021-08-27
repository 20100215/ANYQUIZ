var xhttp = new XMLHttpRequest();
var res;

function signupsubmit(e) {
    var url = "./src/signupaction.php";
    var data = $("#signupform").serialize();
    var urlData = url + "?" + data;
    xhttp.open("POST", urlData, true);
    xhttp.send();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            res = JSON.parse(this.responseText);
            if (res['status'] == 200) {
                $("#popupalert").hide();
                $('#signupsuccess').modal('show');
            } else {
                $("#alertcontent").html(res['message']);
                $("#popupalert").show();
                setTimeout(function () { $("#popupalert").fadeOut(1000); }, 4000);
            }
        }
    };
    e.preventDefault();
}

function signinsubmit(e) {
    var url = "./src/signinaction.php";
    var data = $("#signinform").serialize();
    var urlData = url + "?" + data;
    var success = 0;
    xhttp.open("POST", urlData, true);
    xhttp.send();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            res = JSON.parse(this.responseText);
            $("#signinform")[0].reset();
            if (res["status"] == 200) {
                success = 1;
                location.replace('index.php');
            } else {
                $("#alertcontent").html(res['message']);
                $("#popupalert").show();
                setTimeout(function () { $("#popupalert").fadeOut(1000); }, 2000);
            }
        }
    }
    e.preventDefault();
}

function signoutClick(e) {
    var url = "./src/signoutaction.php";
    xhttp.open("GET", url, true);
    xhttp.send();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            res = JSON.parse(this.responseText);
            if (res['status'] == 200) {
                location.replace('login.php');
            }
        }
    };
}

function updateAccount(e) {
    var url = "./src/updateaccountaction.php";
    var data = $("#updateaccount").serialize();
    var urlData = url + "?" + data;
    xhttp.open("POST", urlData, true);
    xhttp.send();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            res = JSON.parse(this.responseText);
            var x = (res['status'] == 200) ? "success" : "alert";
            var y = (res['status'] == 200) ? "alert" : "success";
            $("#" + x + "content").html(res['message']);
            $('[name=oldpassword]').val('');
            $('[name=password]').val('');
            $('[name=confirmpassword]').val('');
            $("#popup" + y).hide();
            $("#popup" + x).show();
            setTimeout(function () { $("#popup" + x).fadeOut(1000); }, (x == "success") ? 15000 : 4000);
        }
    };
    e.preventDefault();
}

/* From contact form */
function contactformsubmit(e) {
    var url = "./src/contactformaction.php";
    var data = $("#contactForm").serialize();
    var urlData = url + "?" + data;
    xhttp.open("POST", urlData, true);
    xhttp.send();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            res = JSON.parse(this.responseText);
            if (res['status'] == 200) {
                $('#alertfailed').hide();
                $('#alertsuccess').show();
                $('.submit').prop("disabled", true);
                $('#contactForm').prop("disabled", true);
            } else {
                $('#alertsuccess').hide();
                $('#alertfailed').html("<i class='icon_error-circle'></i>&nbsp; &nbsp;" + res['message']);
                $('#alertfailed').show();
            }
        }
    };
    e.preventDefault();
}
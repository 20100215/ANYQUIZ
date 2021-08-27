function triggeralert(myWidth,myTime,myMessage) {
    $("#alertcontent").html(myMessage);
    $("#alertcontent").css({"width":myWidth+"px"});
    $("#popupsuccess").hide();
    $("#popupinfo").hide();
    $("#popupalert").show();
    setTimeout(function () { $("#popupalert").fadeOut(1000); }, myTime);
}

function triggersuccess(myWidth,myTime,myMessage) {
    $("#successcontent").html(myMessage);
    $("#successcontent").css({"width":myWidth+"px"});
    $("#popupalert").hide();
    $("#popupinfo").hide();
    $("#popupsuccess").show();
    setTimeout(function () { $("#popupsuccess").fadeOut(1000); }, myTime);
}

function triggerinfo(myWidth,myTime,myMessage) {
    $("#infocontent").html(myMessage);
    $("#infocontent").css({"width":myWidth+"px"});
    $("#popupalert").hide();
    $("#popupsuccess").hide();
    $("#popupinfo").show();
    setTimeout(function () { $("#popupinfo").fadeOut(1000); }, myTime);
}
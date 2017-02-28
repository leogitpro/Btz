
$(function () {
    //alert('add qrcode');
    $(".qrcode-type").change(function () {
        if("none" == $("#expired-row").css("display")) {
            $("#expired-row").show();
        } else {
            $("#expired-row").hide();
        }
    });
})
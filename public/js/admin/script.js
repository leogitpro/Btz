
function globalFuncMoveToCentered()
{
    var ww = $(window).width();
    var wh = $(window).height();

    var dw = $("#centered_container").width();
    var dh = $("#centered_container").height();

    var left = 0;
    var top  = 0;
    if(ww > dw) {
        left = (ww - dw) / 2;
    }
    if(wh > dh) {
        top = ((wh - dh) / 2) * 0.8;
    }

    $("#centered_container").css({"left": left, "top": top});

}

$(function(){
    globalFuncMoveToCentered();
    $(window).resize(function(){
        globalFuncMoveToCentered();
    });
});

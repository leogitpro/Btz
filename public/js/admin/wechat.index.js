
$(function () {
   $(".check-wechat").click(function () {
       var url = $(this).attr("href");
       $(this).blur().attr("disabled", true).removeAttr("href");
       $.get(url, function (dt) {
           console.log(dt);
           window.location.reload(true);
       }, "json");
       return false;
   });
});

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


   $(".remove-client").click(function () {
      if(confirm("确定要删除这个来访客户端配置么? 删除后立即生效, 不可恢复!")) {
          return true;
      } else {
          return false;
      }
   });


});
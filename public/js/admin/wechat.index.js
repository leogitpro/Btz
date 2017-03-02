
$(function () {
   $("#validateWeChat").click(function () {
       var url = $(this).attr("href");
       $(this).blur().attr("disabled", true).removeAttr("href");
       $.get(url, function (dt) {
           console.log(dt);
           if(!dt.success) {
               alert("当前公众号配置未能通过微信验证. 请确认公众号配置是否正确.\n或者还有其他的平台授权操作公众号 Token?");
           }
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
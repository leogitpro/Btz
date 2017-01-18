
$(function () {
   $("#pageModal").on("loaded.bs.modal", function (e) {
       //console.log(e.data);
   });

   // clean modal data force every time load newest data from server.
   $("#pageModal").on("hidden.bs.modal", function() {
      $(this).removeData("bs.modal");
   });

   $("body").on("click", "#save-dept-member", function () {
       var url = $(this).attr("path");
       var _target = $(this);

       var member_ids = new Array();
       $("#member-department-container").find("input[type='checkbox']").each(function () {
          if (this.checked) {
              member_ids.push(this.value);
          }
       });

       $(this).attr("disabled", true);
       $.post(url, {"selected": member_ids}, function (dt) {
            if (dt.success) {
               console.log(dt);
                $("#pageModal").modal('hide');
                document.location.reload();
                //$("#pageModal").removeData("bs.modal");
            } else {
                alert("Save changes failed, Please try again!");
                _target.removeAttr("disabled");
            }
       }, 'json');

   });
});
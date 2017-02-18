
$(function () {

    // clean modal data force every time load newest data from server.
    $("#pageModal").on("hidden.bs.modal", function() {
        $(this).removeData("bs.modal");
    });

    $("body").on("click", ".remove-action", function () {
        var tr = $(this).parent().parent();
        if (confirm("确定要删除么? 操作不可恢复!")) {
            var url = $(this).attr("href");
            $(this).blur();
            $.get(url, function (dt) {
                tr.hide();
            });
            return false;
        } else {
            $(this).blur();
            return false;
        }
    });


    $(".danger-link").click(function () {
        if (confirm("确定要删除么? 操作不可恢复!")) {
            return true;
        } else {
            $(this).blur();
            return false;
        }
    });



    $("#sync-link").click(function(){
        if ("disabled" == $(this).attr("disabled")) {
            return false;
        }

        var url = $(this).attr("href");

        $(this).children("i").addClass("fa-spin");
        $(this).attr("href", "#");
        $(this).attr("disabled", true);

        //Ajax post
        $.get(url, function (dt) {
            location.reload();
        });

        return false;
    });
});
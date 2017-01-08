
$(function () {
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
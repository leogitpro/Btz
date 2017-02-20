
$(function () {
    $(".message-modal").on("shown.bs.modal", function (e) {

        $(e.relatedTarget).html($(e.relatedTarget).text());
        var url = $(e.relatedTarget).data("ajaxcall");

        $(e.relatedTarget).parent().prev().prev().children('i').removeClass("fa-envelope-o").addClass("fa-envelope-open-o");

        if(url.length > 36) {
            $.get(url, function (dt) {
                console.log(dt);
            }, "json");
            $(e.relatedTarget).data("ajaxcall", "");
        }

    });

    $(".message-modal").on("hidden.bs.modal", function () {
        window.setTimeout(function () {
            $(".show-message-content").each(function () {
                $(this).blur();
            });
        }, 100);
    });

    $(".delete-message-dialog").click(function () {
        var dialogId = $(this).data("target");
        var url = $(this).data("ajaxcall");
        $.get(url, function (dt) {
            if(dt.success) {
                window.setTimeout(function () {
                    $("#box_" + dialogId).hide();
                }, 500);
            }
            console.log(dt);
        }, 'json');
        $('#' + dialogId).modal('hide');
    });
});
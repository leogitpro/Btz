
$(function () {

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
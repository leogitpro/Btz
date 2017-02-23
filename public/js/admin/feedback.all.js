
$(function () {
    $(".remove-link").click(function () {
        if(confirm("确定要删除这个反馈么? 删除之后不能再恢复!")) {
            return true;
        } else {
            return false;
        }
    });

    $(".update-reply").click(function () {
        var text = $("#reply_" + $(this).attr("name")).val();
        var url = $(this).data("path");

        $(this).blur();

        $.post(url, {content:text}, function (dt) {
            console.log(dt);
            if(dt.success) {
                window.location.reload(true);
            }
        }, "json");
    });
});
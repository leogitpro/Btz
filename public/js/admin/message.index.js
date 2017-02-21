
$(function () {
    $(".message-modal").on("hidden.bs.modal", function () {
        window.setTimeout(function () {
            $(".show-message-content").each(function () {
                $(this).blur();
            });
        }, 100);
    });

    $(".close-message").click(function () {
        var url = $(this).attr("href");
        var curLine = $(this).parent().parent();
        if(confirm("确定关闭此条消息么? 所有拥有此消息的人均无法再查阅!")) {
            $.get(url, function (dt) {
                console.log(dt);
                if(dt.success) {
                    curLine.hide();
                } else {
                    alert(dt.message);
                }
            }, 'json');
        }

        return false;
    });
});
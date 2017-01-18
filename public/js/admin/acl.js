
$(function () {
    $(".action-status").change(function () {
        var url = $(this).val();
        $(this).blur();
        $.get(url, function (dt) {
            if(!dt.success) {
                alert('System error!' + "\n" + dt.message);
            }
        }, 'json');
    });
});

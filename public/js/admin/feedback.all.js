
$(function () {
    $(".remove-link").click(function () {
        if(confirm("确定要删除这个反馈么? 删除之后不能再恢复!")) {
            return true;
        } else {
            return false;
        }
    });
});
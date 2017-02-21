
$(function () {
    $("#receiver_name").bootcomplete({
        url: autoSuggestUrl,
        method: "post",
        idField: true,
        idFieldName: 'receiver_id',
        minLength: 1
    });
});
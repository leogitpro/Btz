
$(function(){
    $("form").submit(function () { // forbid re-submit
        $(":submit", this).attr("disabled","disabled");
    });
});
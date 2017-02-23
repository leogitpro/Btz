

function buildMessageMenuItem(author, topic, time, unread, url) {
    if(unread) {
        var topicRow = "<div><strong>" + topic + "</strong></div>";
    } else {
        var topicRow = "<div>" + topic + "</div>";
    }
    var authorRow = "<div><strong>" + author + "</strong><span class='pull-right text-muted'><em>" + time + "</em></span></div>";
    return "<li><a href='" + url + "'>" + authorRow + topicRow + "</a></li><li class='divider'></li>";
}

function buildMessageMenu(response) {
    if(!response.success) {
        return "";
    }

    var count = response.count;
    var menuHtml = "";
    var itemsCount = response.rows.length;
    if(itemsCount < 1) {
        return "";
    }
    for(var i=0; i < itemsCount; i++) {
        menuHtml += buildMessageMenuItem(response.rows[i].sender, response.rows[i].topic, response.rows[i].time, response.rows[i].unread, response.inboxUrl);
    }

    var menu = '<li class="dropdown">';
    menu += '<a class="dropdown-toggle" href="#" data-toggle="dropdown" aria-expanded="true">';
    menu += '<i class="fa fa-envelope fa-fw"></i> 消息';
    if(parseInt(count)) {
        menu += ' <span class="badge">' + count + '</span> ';
    }
    menu += ' <i class="fa fa-caret-down"></i>';
    menu += '</a>';
    menu += '<ul class="dropdown-menu dropdown-messages">';
    menu += menuHtml;
    menu += '<li><a class="text-center" href="' + response.inboxUrl + '">';
    menu += '<strong>查阅全部消息</strong><i class="fa fa-angle-right fa-fw"></i>';
    menu += '</a></li></ul></li>';

    return menu;
}



$(function(){
    $("form").submit(function () { // forbid re-submit
        $(":submit", this).attr("disabled","disabled");
    });

    //Ajax request top message menu.
    var messageMenuUrl = $("#topMenuContainer").data("message");
    $.get(messageMenuUrl, function (dt) {
        var html = buildMessageMenu(dt);
        if(html.length) {
            $(html).prependTo($("#topMenuContainer"));
        }
    }, "json");
});


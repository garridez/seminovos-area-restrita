module.exports.seletor = ".l-layout";

module.exports.callback = $ => {
    require("bootstrap/js/dist/util.js");
    require("bootstrap/js/dist/collapse");
    require("bootstrap/js/dist/dropdown");
    require("components/pagesQuickView")($);
    require("components/pagesMobileView")($);
    require("components/pagesChat")($);

    if ($(window).width() < 992) {
        $("body").removeClass("desktop");
        $("body").addClass("mobile");
    } else {
        $("body").addClass("desktop");
        $("body").removeClass("mobile");
    }
    $(".toggle-sidebar").click(e => {
        if ($("body").hasClass("sidebar-open")) {
            $("body").removeClass("sidebar-open");
            $(".page-sidebar").removeClass("visible");
        } else {
            $("body").addClass("sidebar-open");
            $(".page-sidebar").addClass("visible");
        }
    });

    let menu = $(".menu-items");
    menu.find();


    // (function () {
    //     var title = $('title');
    //     title.data('original', title.html());

    //     var prevSetAjaxLoadding = window.setAjaxLoadding;
    //     window.setAjaxLoadding = false;

    //     function updateCount() {
    //         $.getJSON('/chat/nao-lidas', function (data) {
    //             window.setAjaxLoadding = prevSetAjaxLoadding;
    //             var iconMsgCount = $('.sidebar-menu ul li.menu-chat .icon-thumbnail i');
    //             console.log(data)

    //             if (!data.total) {
    //                 title.html(title.data('original'));
    //                 iconMsgCount.html('');
    //                 return;
    //             }

    //             var titleText = title.data('original');

    //             title.html('(' + data.total + ') ' + titleText);
    //             iconMsgCount.html(' ' + data.total);
    //         });
    //     }
    //     updateCount();
    //     setInterval(updateCount, 15000);
    // })();
};

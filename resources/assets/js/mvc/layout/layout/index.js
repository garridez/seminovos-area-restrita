module.exports.seletor = '.l-layout';

module.exports.callback = ($) => {
    require('bootstrap/js/dist/util.js');
    require('bootstrap/js/dist/collapse');
    if ($(window).width() < 992) {
        $("body").removeClass("desktop");
        $("body").addClass("mobile");
    }
    else {
        $("body").addClass("desktop");
        $("body").removeClass("mobile");
    }
    $(".toggle-sidebar").click((e) => {
        if ($("body").hasClass("sidebar-open")) {
            $("body").removeClass("sidebar-open");
            $(".page-sidebar").removeClass("visible");
        } else {
            $("body").addClass("sidebar-open");
            $(".page-sidebar").addClass("visible");
        }
    });

    let menu = $(".menu-items");
    menu.find()
};
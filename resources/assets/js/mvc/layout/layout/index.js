module.exports.seletor = '.l-layout';

module.exports.callback = ($) => {
    require('bootstrap/js/dist/util.js');
    require('bootstrap/js/dist/collapse');
    require('bootstrap/js/dist/dropdown');
    require('../../../components/pagesQuickView')($);
    require('../../../components/pagesMobileView')($);
    require('../../../components/pagesChat')($);
    const jsCookie = require('js-cookie');

    if ($(window).width() < 992) {
        $('body').removeClass('desktop');
        $('body').addClass('mobile');
    } else {
        $('body').addClass('desktop');
        $('body').removeClass('mobile');
    }
    $('.toggle-sidebar').click(() => {
        if ($('body').hasClass('sidebar-open')) {

            if($('body').hasClass('mobile')) {
                jsCookie.remove('sidebar-collapsed');
            }

            $('body').removeClass('sidebar-open');
            $('.page-sidebar').removeClass('visible');
        } else {
            $('body').addClass('sidebar-open');
            $('.page-sidebar').addClass('visible');
        }
    });

    let menu = $('.menu-items');
    menu.find();
    $('.sidebar-menu .btn-sidebar-collapse').click(function () {

        if($('body').hasClass('mobile')) {
            jsCookie.remove('sidebar-collapsed');

            if ($('body').hasClass('sidebar-open')) {
                $('body').removeClass('sidebar-open');
                $('.page-sidebar').removeClass('visible');
            }
        } else {
            $('body').toggleClass('sidebar-collapsed');

            if ($('body').hasClass('sidebar-collapsed')) {
                jsCookie.set('sidebar-collapsed', '1', {
                    expires: 365,
                });
            } else {
                jsCookie.remove('sidebar-collapsed');
            }
        }
    });

    (function () {
        var title = $('title');
        title.data('original', title.html());

        var prevSetAjaxLoadding = window.setAjaxLoadding;
        window.setAjaxLoadding = false;

        function updateCount() {
            $.getJSON('/chat/nao-lidas', function (data) {
                window.setAjaxLoadding = prevSetAjaxLoadding;
                var iconMsgCount = $('.sidebar-menu ul li.menu-chat .count');

                if (!data.total) {
                    title.html(title.data('original'));
                    iconMsgCount.html('');
                    return;
                }

                var titleText = title.data('original');

                title.html('(' + data.total + ') ' + titleText);
                iconMsgCount.html(' ' + data.total);
            });
        }
        updateCount();
        setInterval(updateCount, 10_000);
    })();
};

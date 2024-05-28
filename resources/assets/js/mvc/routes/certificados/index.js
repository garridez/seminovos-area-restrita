module.exports.seletor = '.c-certificados.a-index';
module.exports.callback = ($) => {
    jQuery = require('jquery');
    require('printthis');
    $(document).bind('keydown', function (e) {
        if (e.ctrlKey && e.keyCode == 80) {
            $('.to-print').printThis({
                loadCSS: '/css/app.css',
            });
            return false;
        }
    });
    $('.btn-print').click(function () {
        $('.to-print').printThis({
            loadCSS: '/css/app.css',
        });
    });
};

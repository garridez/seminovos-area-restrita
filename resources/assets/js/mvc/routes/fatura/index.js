
module.exports.seletor = '.c-fatura.a-particular';
module.exports.callback = ($) => {
    jQuery = require('jquery');
    require('printthis');
    $(document).bind("keydown", function (e) {
        if (e.ctrlKey && e.keyCode == 80) {
            $('.card-body').printThis({
                loadCSS: "/css/app.css"
            });
            return false;
        }
    });
    $('.btn-print').click(function () {
        $('.card-body').printThis({
            loadCSS: "/css/app.css"
        });
    })
}

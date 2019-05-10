
module.exports.seletor = '.c-fatura.a-particular';
module.exports.callback = ($) => {
    jQuery = require('jquery');
    require('printthis');
    $('.btn-print').click(function () {
        $('.card-body').printThis({
            loadCSS: "/css/app.css"
        });
    })
}

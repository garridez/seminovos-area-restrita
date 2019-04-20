module.exports.seletor = '.c-criar-anuncio.a-index';

module.exports.callback = ($) => {
    require('bootstrap/js/dist/util.js');
    require('bootstrap/js/dist/collapse.js');

    var handlers = {
        cc: require('./checkout/cartao-de-credito'),
        pagseguro: require('./checkout/pagseguro'),
        transferenciaFinalizar: require('./checkout/transferencia-finalizar'),
        transferenciaUpload: require('./checkout/transferencia-upload'),
    };

    var initialized = false;
    function init() {
        if (initialized) {
            return;
        }
        initialized = true;

        handlers.cc();
        handlers.pagseguro();
        handlers.transferenciaFinalizar();
        handlers.transferenciaUpload();
    }
    $('.step-container')
            .on('step:change:checkout', function (e) {
                init();
                $('.btn-continuar')
                        .removeClass('btn-laranja')
                        .attr('disabled', true);
            })
            .on('step:pre-exit:checkout', function (e) {
                $('.btn-continuar')
                        .addClass('btn-laranja')
                        .attr('disabled', false);
            });

};
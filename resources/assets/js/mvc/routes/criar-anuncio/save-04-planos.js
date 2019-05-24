
module.exports.seletor = '.c-criar-anuncio.a-index';

module.exports.callback = ($) => {
    var stopEvent = require('helpers/StopEvent');
    var alerts = require('components/Alerts');

    $('.anuncio-steps').on('click', '.step-plano label[data-plano-desativado]', function () {
        alerts.info('Não é possível diminuir o plano');
    });

    $('.step-container').on('step:pre-exit:plano', function (e) {
        var ctx = $('.step-plano');
        var plano = ctx.find('[name="idPlano"]:checked');
        var idPlano = parseInt(plano.val(), 10);
        $('#dados-basicos .idPlano').val(idPlano);
        $('#dados-basicos .total').val(plano.data('valor-plano'));

        // Se for grátis
        if (idPlano === 1) {
            window.location.href = '/carro/checkout/gratis';
            return stopEvent(e);
        }
    });
};
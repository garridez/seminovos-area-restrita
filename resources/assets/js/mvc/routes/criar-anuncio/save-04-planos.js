/**
 * Aqui é manipulado toda a parte de upload das imagens
 */
module.exports.seletor = '.c-criar-anuncio.a-index';

module.exports.callback = ($) => {

    var alerts = require('components/Alerts');

    $('.anuncio-steps').on('click', '.step-plano label[data-plano-desativado]', function () {
        alerts.info('Não é possível diminuir o plano');
    });

    $('.step-container').on('step:pre-exit:plano', function (e) {
        var ctx = $('.step-plano');
        var plano = ctx.find('[name="idPlano"]:checked');
        $('#dados-basicos .idPlano').val(plano.val());
        $('#dados-basicos .total').val(plano.data('valor-plano'));

    });
};
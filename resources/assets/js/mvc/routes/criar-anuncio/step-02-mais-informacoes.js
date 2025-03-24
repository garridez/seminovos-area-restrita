import $ from 'jquery';
module.exports.seletor = '.c-criar-anuncio.a-index';

module.exports.callback = ($) => {
    $('.step-container').on('steps-loaded', init);

    var DataLayerGTMPopulate = require('../../../helpers/DataLayerGTMPopulate');

    $('.step-container').on('step:pre-exit:fotos', function () {
        if ($('#dados-basicos #flagCriando').val() == 1) {
            var ctx = $('.step-0, .step-1');
            DataLayerGTMPopulate(ctx, 'checkout_step_4');
        }
    });
};

function init() {
    var ctx = $('.step-preco');
    var observacoesTextarea = ctx.find('textarea[name="observacoes"]');
    var countSpan = ctx.find('.wordcount span.count');
    var BtnContinuar = require('./helpers/BtnContinuar');
    observacoesTextarea
        .keyup(function () {
            countSpan.html($(this).val().length);
            if ($(this).val().length > 650) {
                countSpan.html(
                    '<span style="color:red;font-size:13px;">Contém ' +
                        $(this).val().length +
                        ' caracteres - As observações não podem ultrapassar 650 caracteres</span>',
                );
            } else {
                BtnContinuar.enable();
            }
        })
        .keyup();
}

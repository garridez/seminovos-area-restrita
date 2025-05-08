import $ from 'jquery';

import DataLayerGTMPopulate from '../../../helpers/DataLayerGTMPopulate';
import BtnContinuar from './helpers/BtnContinuar';

export const seletor = '.c-criar-anuncio.a-index';
export const callback = ($) => {
    $('.step-container').on('steps-loaded', init);

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

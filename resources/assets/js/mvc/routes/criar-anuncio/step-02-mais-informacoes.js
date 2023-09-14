const BtnContinuar = require("./helpers/BtnContinuar");
module.exports.seletor = '.c-criar-anuncio.a-index';

module.exports.callback = ($) => {
    $('.step-container').on('steps-loaded', init);

    var DataLayerGTMPopulate = require('helpers/DataLayerGTMPopulate');

    $('.step-container').on('step:pre-exit:fotos', function(){
      if($('#dados-basicos #flagCriando').val() == 1){
        var ctx = $('.step-0, .step-1');
        DataLayerGTMPopulate(ctx,'checkout_step_4');
      }
    });
};

function init() {
    var ctx = $('.step-preco');
    var observacoesTextarea = ctx.find('textarea[name="observacoes"]');
    var countSpan = ctx.find('.wordcount span.count');
    var BtnContinuar = require('./helpers/BtnContinuar');
    observacoesTextarea.keyup(function () {
        countSpan.html($(this).val().length);
        if ($(this).val().length > 700) {
            BtnContinuar.disable()
            countSpan.html($(this).val().length + ` - As observações não podem ultrapassar 700 caracteres`);
        } else {
            BtnContinuar.enable()
        }
    }).keyup();
}

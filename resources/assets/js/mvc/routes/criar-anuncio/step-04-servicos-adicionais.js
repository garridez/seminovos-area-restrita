module.exports.seletor = '.c-criar-anuncio.a-index';
module.exports.prepend = true;

module.exports.callback = ($) => {
    $('.step-container').on('steps-loaded', init);
};

function init() {
    var BtnContinuar = require('./helpers/BtnContinuar');
    var $ctx = $('#form_servicos-adicionais');
    
    $('.step-container').on('step:pre-change:servicos-adicionais', function (e) {
      
      if(window.fromCheckout){
        BtnContinuar.get().removeClass('hide d-none');
        BtnContinuar.enable();
      }
    });

    $('input#servico-adicional-certificado').change(function () {
        var $this = $(this);

        var adicionar = $ctx.find('.btn-control-certificado .text-adicionar');
        var adicionado = $ctx.find('.btn-control-certificado .text-adicionado');

        adicionar.removeClass('hide');
        adicionado.removeClass('hide');

        if ($this.is(':checked')) {
            adicionar.hide();
            adicionado.show();
            $('.btn-continuar').click();
        } else {
            adicionado.hide();
            adicionar.show();
            $('.btn-continuar').click();
        }


        $('[data-adicionar-action]').prop('checked', $this.is(':checked'));
        $('#dados-basicos .certificado').val($this.is(':checked') ? 1 : '');


    });
}
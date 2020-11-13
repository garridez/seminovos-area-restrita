
/**
 *
 * Finaliza o pagamento para o cliente enviar o comprovante depois
 */
module.exports = function () {
  var ctx = '.pagamento-transferencia-form';
  $(ctx + ' button.pagamento-finalizar-deposito').click(function (e) {
      $(ctx + ' .form-control-file').prop('required', false);
  });
  $(ctx + ' button.btn-submit-pagt').click(function (e) {
      $(ctx + ' .form-control-file').prop('required', true);
  });
  $('button.btn-continuar.btn.btn-lg.btn-laranja').addClass('d-none');




  // handler certificado no checkout
  ctx = ' .step-checkout ';
  let flagCertificado = $(ctx + '.flagCertificado');
  
  let certificado = $('#dados-basicos .certificado');
  
  let acao = $('#dados-basicos .acao');

  let classAcitive = 'remove-certificado';

  let resumoContainer = $(ctx +'.resumo-compra');
  let certificadoContainer = $(ctx + '.handle-certificado');

  let btnControlCertificado = $(ctx +'.btn-control-certificado a');

  btnControlCertificado.on('click',function(e){

    e.preventDefault();
    let remover = flagCertificado.is(':checked');
    
    if(acao.val() == 'addCertificado'){
        $('.valor-total').find('[data-valor-total]').html('29,90');
    }

    resumoContainer.addClass(classAcitive);
    certificadoContainer.addClass(classAcitive);
    flagCertificado.prop('checked','checked');
    certificado.val(1);

    if(remover){
      resumoContainer.removeClass(classAcitive);
      certificadoContainer.removeClass(classAcitive);
      flagCertificado.prop('checked',false);
      certificado.val('');
    }

  });
  // handler modal certificado

  let btnSaibaMais = $(ctx + '.saiba-mais a');

  btnSaibaMais.on('click',function(e){
    e.preventDefault();
    $('body').prepend($('<div class="modal-fade"></div>'));
    $(ctx).addClass('modal-open');

  });
  let btnModalClose = $(ctx + '.modal-sobre-certificado .close');
  btnModalClose.on('click',function(e){
    e.preventDefault();
    $('body').find('.modal-fade').remove();
    $(ctx).removeClass('modal-open');

  });

};


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
};

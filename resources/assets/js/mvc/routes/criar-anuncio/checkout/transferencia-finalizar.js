import $ from 'jquery';

/**
 *
 * Finaliza o pagamento para o cliente enviar o comprovante depois
 */
module.exports = function () {
    var ctx = '.pagamento-transferencia-form';
    $(ctx + ' button.pagamento-finalizar-deposito').click(function () {
        $(ctx + ' .form-control-file').prop('required', false);
    });
    $(ctx + ' button.btn-submit-pagt').click(function () {
        $(ctx + ' .form-control-file').prop('required', true);
    });
    $('button.btn-continuar.btn.btn-lg.btn-laranja').addClass('d-none');
};

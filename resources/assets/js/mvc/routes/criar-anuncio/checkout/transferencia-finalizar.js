import $ from 'jquery';

/**
 *
 * Finaliza o pagamento para o cliente enviar o comprovante depois
 */
export default function () {
    var ctx = '.pagamento-transferencia-form';
    $(ctx + ' button.pagamento-finalizar-deposito').on('click', function () {
        $(ctx + ' .form-control-file').prop('required', false);
    });
    $(ctx + ' button.btn-submit-pagt').on('click', function () {
        $(ctx + ' .form-control-file').prop('required', true);
    });
    $('button.btn-continuar.btn.btn-lg.btn-laranja').addClass('d-none');
}

import 'jquery-mask-plugin';
import 'jquery-validation';
import 'jquery-validation/dist/additional-methods';
import 'jquery-validation/dist/localization/messages_pt_BR';

import $ from 'jquery';

import requestPagamento from './request-pagamento';

export default function () {
    var optional = { translation: { '?': { pattern: /[0-9]/, optional: true } } };
    var formCC = $('.pagamento-cc-form');

    formCC.find('[name="validade_cartao"]').mask('00/00');
    formCC.find('[name="cvc_cartao"]').mask('999?', optional);
    formCC.find('[name="numero_cartao"]').mask('9999 9999 9999 9??? ????', optional);
    formCC.find('[name="cep"]').mask('9999999999?', optional);
    formCC.validate({
        rules: {
            numero_cartao: {
                required: true,
                creditcard: true,
            },
            cep: {
                required: true,
            },
        },
        messages: {
            termos: 'É preciso ler e aceitar os termos para continuar',
        },
        submitHandler: function (form, event) {
            event.preventDefault();
            requestPagamento($(form).serializeArray());
            return false;
        },
    });
}

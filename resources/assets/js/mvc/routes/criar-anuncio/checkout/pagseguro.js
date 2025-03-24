import $ from 'jquery';

export default function () {
    var requestPagamento = require('./request-pagamento');
    $('form.pagamento-boleto-form').submit(function (e) {
        e.preventDefault();
        if (!$(this).valid()) {
            return;
        }
        requestPagamento([
            {
                name: 'metodo',
                value: 'boleto',
            },
        ]);
    });
};

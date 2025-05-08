import $ from 'jquery';

import requestPagamento from './request-pagamento';

export default function () {
    $('form.pagamento-pix-form').on('submit', function (e) {
        e.preventDefault();
        if (!$(this).valid()) {
            return;
        }
        requestPagamento([
            {
                name: 'metodo',
                value: 'pix',
            },
        ]);
    });
}

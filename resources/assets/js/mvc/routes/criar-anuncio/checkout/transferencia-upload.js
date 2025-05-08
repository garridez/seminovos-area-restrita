import $ from 'jquery';

import requestPagamento from './request-pagamento';
export default function () {
    $('.pagamento-transferencia-form').submit(function (e) {
        e.preventDefault();
        if (!$(this).valid()) {
            return;
        }
        var data = new FormData();
        $(this)
            .find('input[type="hidden"]')
            .each(function () {
                data.append(this.name, this.value);
            });

        $.each($(this).find('#comprovanteAnexo')[0].files, function (_key, value) {
            data.append('comprovanteAnexo', value);
        });

        $('#dados-basicos form input').each(function () {
            data.append(this.name, this.value);
        });

        requestPagamento(null, {
            data: data,
            cache: false,
            processData: false,
            contentType: false,
        });
    });
}

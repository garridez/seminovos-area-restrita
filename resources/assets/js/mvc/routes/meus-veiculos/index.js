
module.exports.seletor = '.c-meus-veiculos.a-index';

module.exports.callback = ($) => {
    require('components/JsBsModal');
    var Alerts = require('components/Alerts');
    /**
     * Baixa o conteúdo da página atualizado
     * Baixa apenas o conteúdo dentro da div ".container-anuncios"
     */
    function reloadPageContent() {
        $.get('/', function (data) {
            $('.container-anuncios').replaceWith(data);
        });
        $.get('/meus-veiculos/qtd-anuncios-menu', function (data) {
            $('.qtd-anuncios-menu').html(data);
        });
    }

    $('body').on('click', '.anuncios [data-modal]', function () {
        var modal;
        var $this = $(this);
        var url = $this.data('url');
        var body = $this.data('modal-body');
        var successText = $this.data('modal-success-msg');
        var yesText = $this.data('modal-yes-text') || 'Sim';

        var btnSuccess = $('<button class="btn btn-success">')
                .html(yesText)
                .click(function () {
                    $(this).attr('disabled', true);
                    $.getJSON(url).done(function (data, jqXHR, type) {
                        if (data.status !== 200) {
                            Alerts.error(data.detail, 'Houve um problema...', 10000);
                        } else {
                            Alerts.success(successText);
                            reloadPageContent();
                        }
                    }).fail(function (jqXHR, textStatus, errorThrown) {
                        Alerts.error('Não conseguir uma resposta para sua solicitação. <br> Tente novamente mais tarde.', 'Houve um problema...', 10000);

                    }).always(function () {

                        modal.modal('hide');
                    });
                });

        var footer = [
            '<button class="btn btn-danger" data-dismiss="modal">Cancelar</button>',
            btnSuccess
        ];

        modal = $.jsBsModal({
            contents: {
                'modal-title': 'Atenção',
                'modal-body': body,
                'modal-footer': footer,
            }
        });
    });

};
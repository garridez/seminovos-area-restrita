
module.exports.seletor = '.c-criar-anuncio.a-index';

module.exports.callback = ($) => {
    var HandleApiError = require('components/HandleApiError');
    var Alerts = require('components/Alerts');

    $('.anuncio-steps').on('click', '.step-finalizar .btn-finalizar', function (e) {
        e.preventDefault();
        var status = $(this).data('status');
        var dadosBasicos = $('#dados-basicos');
        var form = $('form', dadosBasicos);
        var dataSerialized = form.serializeArray();
        dataSerialized.push({
            name: 'status',
            value: status
        });

        var acao = status === 2 ? 'ativar' : 'inativar';
        var idVeiculo = dadosBasicos.find('#idVeiculo').val();

        $.ajax({
            type: 'POST',
            url: '/meus-veiculos/' + acao + '/' + idVeiculo,
            data: dataSerialized,
            dataType: 'json',
            success: function (data) {
                if (!HandleApiError(data)) {
                    return;
                }
                var text = 'Seu veículo foi publicado';
                var time = 5000;
                if (status === 5) {
                    text = 'Criação do anúncio concluída!<br>'
                            + 'Seu anúncio não foi publicado/ativado.<br>'
                            + 'Você pode ativa-lo/publica-lo quando quiser através do menu "Meus Veículos"';
                    time = 15000;
                }
                Alerts.success(text, 'Muito bom!', time)
                        .on('hide.bs.modal', function () {
                            window.location.href = '/';
                        });
            },
            error: function (e) {
                if (e.responseJSON) {
                    HandleApiError(e.responseJSON);
                } else {
                    HandleApiError(false);
                }
            }
        });

        return false;


    });
};
module.exports.seletor = '.c-criar-anuncio.a-index';

module.exports.callback = ($) => {
    var HandleApiError = require('components/HandleApiError');
    var advancedAlerts = require('components/AdvancedAlerts');

    $('.anuncio-steps').on('click', '.step-finalizar .btn-finalizar', function (e) {
        e.preventDefault();
        var idStatus = parseInt($(this).data('status'), 10);
        var dadosBasicos = $('#dados-basicos');
        var form = $('form', '#dados-basicos, .step-plano');
        var acao = idStatus === 2 || idStatus === 5 ? 'publicar' : '';
        var idVeiculo = dadosBasicos.find('#idVeiculo').val();
        var dataSerialized = form.serializeArray();
        dataSerialized.push(
            {
                name: 'idStatus',
                value: idStatus,
            },
            {
                name: 'acao',
                value: acao,
            },
        );

        $.ajax({
            type: 'POST',
            url: '/carro/dados',
            data: dataSerialized,
            dataType: 'json',
            success: function (data) {
                if (!HandleApiError(data)) {
                    return;
                }
                var text = 'Seu veículo foi publicado';
                var time = 5000;
                if (idStatus === 5) {
                    text =
                        'Criação do anúncio concluída!<br>' +
                        'Seu anúncio não foi publicado/ativado.<br>' +
                        'Você pode ativa-lo/publica-lo quando quiser através do menu "Meus Veículos"';
                    time = 15000;
                }

                if (data.detail == 'Veiculo Ativado Com Revenda Inativa!') {
                    text =
                        'Seu anúncio foi ativado com sucesso. No entanto, observamos que sua revenda encontra-se <span style="color:red;">inativa</span> no momento.<br><br>Para resolver esta situação e retomar as vendas o quanto antes, por favor acesse a área financeira através do botão abaixo ou entre em contato conosco pelo telefone (31) 99550-2814.<br><br>Obrigado, Equipe Seminovos.<a href="/financeiro" class="btn btn-info btn-block btn-finalizar h-100 mt-3" title="Acessar Financeiro">Acessar Financeiro</a>';
                    time = 25000;
                }

                advancedAlerts
                    .info({ text: text, title: 'Muito bom!', time: time })
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
            },
        });

        return false;
    });
};

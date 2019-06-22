module.exports.seletor = '.c-criar-anuncio.a-index';

module.exports.callback = ($) => {
    var Alerts = require('components/Alerts');
    var stepContainer = $('.step-container');
    stepContainer.on('step:change:checkout', function (e) {
        window.location = '#checkout';
        var planoAtual = $('#dados-basicos #idPlano').val();
        // Se for grátis vai para a tela de selecionar um plano
        if (planoAtual === '1') {
            stepContainer.stepPlugin('goTo', '.step-plano');
            $('#form_Plano input[name="idPlano"][value="1"]')
                    .prop('checked', false)
                    .change(function (e) {
                        if (!confirm('Quer realmente permanecer no plano grátis?')) {
                            e.preventDefault();
                            $(this).prop('checked', false);
                            return false;
                        }
                    });
            Alerts.info('Você está no plano <b>SIMPLES</b>.<br>'
                    + 'Escolha um dos planos de acordo com a <b>prioridade</b> que deseja vender seu veículo<br>'
                    + 'Lembrando que quanto mais <b>alto o plano</b>, mais seu veículo é <b>visto</b> ;)', 'Escolha um plano', 25000);
        }


    }).on('step:exit:checkout', function (e) {
        window.location = '#';
    });
};


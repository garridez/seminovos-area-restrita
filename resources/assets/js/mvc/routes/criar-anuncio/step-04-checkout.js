module.exports.seletor = '.c-criar-anuncio.a-index';

module.exports.callback = ($) => {
    var Alerts = require('components/Alerts');
    var stepContainer = $('.step-container');
    var idPlano = $("#idPlano").val();
    stepContainer.on('step:change:checkout', function (e) {
        var location = window.location;
        if (location.hash && location.hash.indexOf('comprovante') !== -1) {
            var btnTranferencia = $('#accordion-payment [data-target="#transferencia"]');
            btnTranferencia.click();
            setTimeout(function () {
                $("html, body").animate({
                    scrollTop: btnTranferencia.offset().top
                }, 400);
            }, 1000);
        }
        
        if (location.hash && location.hash.indexOf('trocarPlano') !== -1 && location.hash.indexOf('planoNitroHome') !== -1) {
            if(!$('#radio-idPlano-1').is(':checked') && idPlano==4){
                $('#radio-idPlano-1').attr('checked',true);
                $("#idPlano").val(4);
            }
            $("#acao").val("trocarPlano");
            
        }

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


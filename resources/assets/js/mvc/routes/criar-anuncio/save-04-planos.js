
module.exports.seletor = '.c-criar-anuncio.a-index';

module.exports.callback = ($) => {
    var stopEvent = require('helpers/StopEvent');
    var advancedAlerts = require('components/AdvancedAlerts');

    $('.anuncio-steps').on('click', '.step-plano label[data-plano-desativado]', function () {
        advancedAlerts.warning({
            text:'Não é possível diminuir o plano',
            title:$('<span class="text-primary">').html('Atenção!')
        });
    });

    $('.step-container').on('step:change:plano', function () {
        var location = window.location;
        
         if (location.hash && location.hash.indexOf('comprovante') !== -1) {
            var idPlano = location.hash.match(/\d+/)[0];
            $("#idPlano").val(idPlano);
            $("#radio-idPlano-"+idPlano).click();
            $('.step-container').stepPlugin('goTo', '.step-checkout');
        }
        
        if (location.hash && location.hash.indexOf('trocarPlano') !== -1) {
            $("#acao").val("trocarPlano");
        }
    });
    
    $('.step-container').on('step:pre-exit:plano step:change:checkout', function (e) {
        let plano = "planos" + $("#idPlano").val();
        $('[id^="planos"]').each((i, obj) => {
            $(obj).hide();
        });
        $("#" + plano).show();
    });
    $('.step-container').on('step:pre-exit:plano', function (e) {
        var ctx = $('.step-plano');
        var plano = ctx.find('[name="idPlano"]:checked');
        var idPlano = parseInt(plano.val(), 10);
        $('#dados-basicos .idPlano').val(idPlano);
        $('#dados-basicos .total').val(plano.data('valor-plano'));

        // Se for grátis
        if (idPlano === 1) {
            // Salvar todo o formulario anterior as fotos aqui
            var form = $('form', '#dados-basicos,.step-dados,.step-preco,.step-mais-informacoes');
            var dataSerialized = form.serialize();

            $.ajax({
                type: "POST",

                /**
                 * @TODO Corrigir o "/carro" para o valor correto
                 */
                url: "/carro/gratis",
                data: dataSerialized,
                dataType: "json",
                success: function (data) {
                    window.location.href = '/carro/checkout/gratis';
                },
            });

            return stopEvent(e);
        }
    });
};
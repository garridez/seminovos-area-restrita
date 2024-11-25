module.exports.seletor = '.c-criar-anuncio.a-index';

module.exports.callback = ($) => {
    var stopEvent = require('../../../helpers/StopEvent');
    var advancedAlerts = require('../../../components/AdvancedAlerts').default;
    var BtnContinuar = require('./helpers/BtnContinuar');
    var HandleApiError = require('../../../components/HandleApiError').default;

    $('.anuncio-steps').on('click', '.step-plano label[data-plano-desativado]', function (e) {
        advancedAlerts.warning({
            text: 'Não é possível diminuir o plano',
            title: $('<span class="text-primary">').html('Atenção!'),
        });
        stopEvent(e);
    });

    $('.anuncio-steps').on('click', '.step-plano label[data-plano-atual]', function (e) {
        var idStatus = $('#dados-basicos input[name="idStatus"]').val();
        if (idStatus != 10) {
            advancedAlerts.warning({
                text: 'Plano já ativo, selecione outro plano ou clique em voltar',
                title: $('<span class="text-primary">').html('Atenção!'),
            });
            stopEvent(e);
        }
    });

    $('.anuncio-steps').on(
        'click',
        '.step-plano label[data-plano-revenda-desativado]',
        function (e) {
            advancedAlerts.warning({
                text: 'Você atingiu o limite de anúncio disponíveis para este plano',
                title: $('<span class="text-primary">').html('Atenção!'),
            });
            stopEvent(e);
        },
    );

    $('.anuncio-steps').on('click', '.step-plano label', function () {
        BtnContinuar.show();
        BtnContinuar.enable();
    });

    $('.step-container').on('step:pre-change:plano', function () {
        if (window.fromCheckout) {
            BtnContinuar.show();
            BtnContinuar.enable();
        }
    });

    $('.step-container').on('step:change:plano', function () {
        var location = window.location;
        BtnContinuar.disable();
        $('.plano-box input[type="radio"]').on('change', function () {
            if ($(this).is(':checked')) {
                BtnContinuar.enable();
            }
        });

        if (location.hash && location.hash.indexOf('comprovante') !== -1) {
            var idPlano = location.hash.match(/\d+/)[0];
            $('#idPlano').val(idPlano);
            $('#radio-idPlano-' + idPlano).click();
            $('.step-container').stepPlugin('goTo', '.step-checkout');
        }

        if (location.hash && location.hash.indexOf('trocarPlano') !== -1) {
            $('#acao').val('trocarPlano');
        }
    });

    $('.step-container').on('step:pre-exit:plano step:change:checkout', function (ev) {
        let plano = 'planos' + $('#idPlano').val();
        let planoSelecionado = $('#' + plano);

        $('[id^="planos"]').each((i, obj) => {
            $(obj).hide();
            $(obj).removeClass('plano-selecionado');
        });

        planoSelecionado.show();
        planoSelecionado.addClass('plano-selecionado');

        let valorTotal = 0.0;
        if (location.hash.indexOf('addCertificado') === -1) {
            valorTotal += parseFloat(planoSelecionado.find('input[data-valor-plano]').val());
        } else {
            $('.resumo-compra [id*=plano].plano').hide();
        }

        var servicoAdicionalCertificado = $('#servico-adicional-certificado');
        if (servicoAdicionalCertificado.is(':checked')) {
            valorTotal += parseFloat(servicoAdicionalCertificado.data('valor'));
        }
        $('.valor-total')
            .find('[data-valor-total]')
            .html(valorTotal.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' }));

        if (
            ev.type == 'step:change:checkout' &&
            valorTotal === 0 &&
            $('#idPlanoAtual').val() == $('#idPlano').val()

        ) {
            location.hash = 'plano&trocarPlano';
            location.reload();
        }
    });

    $('.step-container').on('step:pre-exit:plano', function (e) {
        var ctx = $('.step-plano');
        var plano = ctx.find('[name="idPlano"]:checked');
        var idPlano = parseInt(plano.val(), 10);
        $('#dados-basicos .idPlano').val(idPlano);
        $('#dados-basicos .total').val(plano.data('valor-plano'));

        var DataLayerGTMPopulate = require('../../../helpers/DataLayerGTMPopulate');
        var ctx2 = $('.step-0, .step-1, .step-plano');
        DataLayerGTMPopulate(ctx2, 'checkout_step_7');
        // Se for grátis
        if (idPlano === 1 || idPlano === 5) {
            // Salvar todo o formulario anterior as fotos aqui
            var form = $('form', '#dados-basicos,.step-dados,.step-preco,.step-mais-informacoes');
            var dataSerialized = form.serialize();

            $.ajax({
                type: 'POST',

                /**
                 * @TODO Corrigir o "/carro" para o valor correto
                 */
                url: '/carro/gratis',
                data: dataSerialized,
                dataType: 'json',
                success: function (data) {
                    if (!HandleApiError(data)) {
                        return;
                    }
                    window.location.href = '/carro/checkout/gratis';
                },
                error: function (e) {
                    if (e.responseJSON) {
                        HandleApiError(e.responseJSON);
                    } else {
                        HandleApiError(false);
                    }
                },
            });

            return stopEvent(e);
        }
    });
};

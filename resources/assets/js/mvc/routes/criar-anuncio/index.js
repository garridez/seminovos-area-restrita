/**
 * Este script faz a configuração inicial da página de anuncie
 */
module.exports.seletor = '.c-criar-anuncio.a-index';
module.exports.prepend = true; // Esse script precisa rodar primeiro
function stopEvent(e) {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    return false;
}
module.exports.callback = ($) => {
    require('components/StepPlugin');
    loadContentStepsAsync();
    var stepsContainer = $('.step-container');

    stepsContainer
            .stepPlugin()
            .on('submit', 'form', function (e) {
                $(this).closest('.step-container').stepPlugin('next');
                return stopEvent(e);
            });
    $('.btn-voltar').on('click', function () {
        $('.step-container [class*="step"].active')
                .closest('.step-container')
                .stepPlugin('prev');
    });
    $('.btn-continuar').on('click', function () {
        let form = stepsContainer.find('[class*="step-"].active form').first();
        form.find('[type="submit"]').first().click();
        let plano = "planos" + $("#idPlano").val();
        $("#" + plano).show();
        if (form[0] && !form[0].checkValidity()) {
            return;
        }
    });
    $('.anuncio-steps').on('steps-loaded', function () {
        var hash = window.location.hash;
        if (!hash) {
            return;
        }
        hash = '.step-' + hash.replace('#', '');

        $('.step-container').each(function () {
            var $this = $(this);
            if ($this.find(hash).length) {
                $this.stepPlugin('goTo', hash);
                $this.closest('[class*="step-"]:not(.anuncio-steps)')
                        .each(function () {
                            $(this)
                                    .parent()
                                    .closest('.step-container')
                                    .stepPlugin('goTo', this);
                        });
            } else {
                var lastStep = $this.stepPlugin('getSteps').last();
                $this.stepPlugin('goTo', lastStep);
            }
        });
    });
    /**
     * NÃO COMMIT O AUTOFILL COMO "true"
     * Isso serve para agilizar o desenvolvimento
     */
    var autofill = false;
    if (autofill) {
        require('./autofill')({
            autofill: true,
            /**
             * Serve para ir passando e parar num step específico
             */
            pararNoStep: 'step-checkout',
            /*
             * Se true, é sempre gerado uma placa nova
             * Útil para não gerar conflito com placa existe
             * Mas cuidado pra não encher de cadastros diferentes
             */
            placaAleatoria: false,
            //placaAleatoria: true,
            // Valor fixo de placa
            placa: 'LJL5173',
            cartao: {
                // Dados de validade do cartão
                validade_cartao: '12/25',
                // É possivel colocar uma data inválida para gerar error e ver as notificações
                //validade_cartao: '19/25',
            },
        });
    }
};
function loadContentStepsAsync() {
    var stepsUrl = $('div.anuncio-steps [data-url]');
    var totalSteps = stepsUrl.length;
    stepsUrl.each(function (i) {
        var ctx = $(this);
        $.get(ctx.data('url'), function (data) {
            ctx.html(data);
            if (--totalSteps === 0) {
                $('.anuncio-steps').trigger('steps-loaded');
            }
        });
    });
}


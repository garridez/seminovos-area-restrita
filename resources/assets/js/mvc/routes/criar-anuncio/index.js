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

    // Troca o ícone ativo de acordo com o step ativo
    stepsContainer.on('step:change', setStepIconActive);
    $('.anuncio-steps').on('steps-loaded', setStepIconActive);

    stepsContainer
            .stepPlugin()
            .on('submit', 'form', function (e) {
                $(this).closest('.step-container').stepPlugin('next');
                return stopEvent(e);
            });
    $('.btn-voltar').on('click', function () {
        var stepContainer = $('.step-container [class*="step"].active')
                .closest('.step-container');
        if ($('.step-dados').is('.active')) {
            window.location.href = '/';
        } else {
            stepContainer.stepPlugin('prev');
        }
    });
    $('.btn-continuar').on('click', function () {
        var form = stepsContainer.find('[class*="step-"].active:not(.step-container) form').first();
        form.find('[type="submit"]').first().click();
        if (form[0] && !form[0].checkValidity()) {
            return;
        }
    });

    stepsContainer
            .on('step:change:checkout step:change:finalizar', function (e) {
                $('.btn-continuar')
                        .removeClass('btn-laranja')
                        .attr('disabled', true);
            })
            .on('step:pre-exit:checkout step:pre-exit:finalizar', function (e) {
                $('.btn-continuar')
                        .addClass('btn-laranja')
                        .attr('disabled', false);
            });
    $('.anuncio-steps').on('steps-loaded', function () {
        var hash = window.location.hash;
        if (!hash || hash === '#') {
            return;
        }
        var hashArr = hash.replace('#','').split('&');
        hash = '.step-' + hashArr[0];

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
             * Serve para ir passando simulando click e parar num step específico
             */
//            pararNoStep: 'step-checkout',
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
    var loading = require('components/Loading');
    var stepsUrl = $('div.anuncio-steps [data-url]');
    var totalSteps = stepsUrl.length;
    loading.open(true);
    stepsUrl.each(function (i) {
        var ctx = $(this);
        ctx.data('timeload', new Date);
        setTimeout(function () {
            $.get(ctx.data('url'), function (data) {
                ctx.html(data);
                if (--totalSteps === 0) {
                    $('.anuncio-steps').trigger('steps-loaded');
                    loading.close(true);
                    setTimeout(function () {
                        loading.close(true);
                    }, 200);
                }
            });

        }, (i * 200) + 10);
    });
}

function setStepIconActive() {
    var stepsIcons = $('.steps-list li');


    $('.anuncio-steps [class*="step-"].active').each(function () {
        var labelStep = $(this).data('step-label');
        if (labelStep) {
            stepsIcons.removeClass('active')
                    .filter(function () {
                        var labels = $(this).data('step').split(',');
                        return labels.indexOf(labelStep) !== -1;
                    })
                    .addClass('active');
        }
    });
}
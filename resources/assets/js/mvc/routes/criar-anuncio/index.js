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
    require('./load-content-steps')();

    var stepsContainer = $('.step-container');
    var anuncioSteps = $('.anuncio-steps'); // Conjuto de steps principal
    var BtnContinuar = require('./helpers/BtnContinuar');

    // Troca o ícone ativo de acordo com o step ativo
    stepsContainer.on('step:change', setStepIconActive);
    stepsContainer.on('step:change', BtnContinuar.enable);
    stepsContainer.on('step:change', checkLastStep);
    anuncioSteps.on('steps-loaded', setStepIconActive);

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
            stepContainer.stepPlugin('prev', false);
        }
        checkLastStep();
    });
    $('.btn-continuar').on('click', function () {
        // BtnContinuar.disable();
        var inLastStep = $(this).data('in-last-step');
        var form = stepsContainer.find('[class*="step-"].active:visible:not(.step-container) form').first();
        form.find('[type="submit"]').first().click();
        if (form[0] && !form[0].checkValidity()) {
            return;
        }

        if (inLastStep) {
            var Loading = require('components/Loading');
            var redirect = function () {
                Loading.open();
                window.location.href = '/meus-veiculos/' + $('#idVeiculo').val();
            };
            // Se não tem ajax pendente, redireciona
            if ($.active === 0) {
                redirect();
                return;
            }
            // Espera todos os ajax terminarem com sucesso para então redirecionar
            $(document).ajaxComplete(function (ev, jqXHR) {
                if (($.active - 1) !== 0) {
                    return;
                }
                if (jqXHR.status > 399) {
                    return;
                }
                if (jqXHR.responseJSON && jqXHR.responseJSON.status !== 200) {
                    return;
                }
                redirect();

            });
        }
    });

    stepsContainer
            .on('step:change:checkout step:change:finalizar', function (e) {
                BtnContinuar.disable();
            })
            .on('step:pre-exit:checkout step:pre-exit:finalizar', function (e) {
                BtnContinuar.enable();
            });
    $('.anuncio-steps').on('steps-loaded', function () {
        var hash = window.location.hash;
        if (!hash || hash === '#') {
            return;
        }
        var hashArr = hash.replace('#', '').split('&');
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
     *
     * Descomentar o autofill apenas em dev!
     */
    //require('./autofill').init();
};
function allStepsInlast() {

    var inLastStep = true;

    $('.step-container').each(function () {
        if (!$(this).stepPlugin('inLastStep')) {
            inLastStep = false;
        }
    });
    return inLastStep;
}
function checkLastStep() {

    var btn = $('.step-controls .btn-continuar');
    var text = 'Continuar';
    var inLast = allStepsInlast();
    if (inLast) {
        text = 'Finalizar';
    } else {
        btn.addClass('btn-laranja').attr('disabled', false);
    }
    btn.text(text).attr('title', text).data('in-last-step', inLast);

}
function setStepIconActive() {
    var $anuncioSteps = $('.anuncio-steps');
    var stepsIcons = $('.steps-list li');

    // Remove os ícones que não tem steps
    stepsIcons = stepsIcons.filter(function () {
        var $this = $(this);
        var stepLabel = $this.data('step').split(',');
        var hasStep = false;
        $.each(stepLabel, function () {
            var seletor = '[data-step-label="' + this + '"]';
            if ($anuncioSteps.find(seletor).length) {
                hasStep = true;
            }
        });
        if (!hasStep) {
            $this.remove();
        }
        return hasStep;
    });
    $anuncioSteps.find('[class*="step-"].active').each(function () {
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

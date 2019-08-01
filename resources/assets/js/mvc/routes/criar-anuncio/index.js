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

    // Troca o ícone ativo de acordo com o step ativo
    stepsContainer.on('step:change', setStepIconActive);
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
            stepContainer.stepPlugin('prev');
        }
    });
    $('.btn-continuar').on('click', function () {
        var form = stepsContainer.find('[class*="step-"].active:not(.step-container) form').first();
        form.find('[type="submit"]').first().click();
        if (form[0] && !form[0].checkValidity()) {
            return;
        }

        if ($('.anuncio-steps').stepPlugin('inLastStep')) {
            var step = form.closest('[data-step-label]');
            var stepLabel = step.data('step-label');
            step.closest('.step-container').trigger('step:pre-exit:' + stepLabel);
            console.log(step.closest('.step-container'))
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
function checkLastStep() {
    var text = 'Continuar';
    if ($('.anuncio-steps').stepPlugin('inLastStep')) {
        text = 'Finalizar';
    }
    $('.step-controls .btn-continuar').text(text).attr('title', text);

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
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
        var form = stepsContainer.find('[class*="step-"].active > form');
        form.find('[type="submit"]').first().click();
        if (form[0] && !form[0].checkValidity()) {
            return;
        }
    });
    
    $('.anuncio-steps').on('steps-loaded', populate);
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


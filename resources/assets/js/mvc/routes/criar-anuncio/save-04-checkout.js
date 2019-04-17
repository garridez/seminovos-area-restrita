module.exports.seletor = '.c-criar-anuncio.a-index';
function stopEvent(e) {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    return false;
}
function setValidation(ctx) {
    $.validator.setDefaults({
        highlight: function (element) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function (element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        errorClass: 'help-block'
    });

    ctx.validate({
        rules: {
            numero_cartao: {
                required: true,
                creditcard: true
            }
        },
        messages: {
            termos: 'É preciso ler e aceitar os termos para continuar'
        },
            event.preventDefault();
            requestPagXamento($(form).serialize());
            return false;
        }
    })
}
function setMasks(ctx) {
    var optional = {translation: {'?': {pattern: /[0-9]/, optional: true}}};


    ctx.find('[name="validade_cartao"]').mask("00/00");
    ctx.find('[name="cvc_cartao"]').mask("999?", optional);
    ctx.find('[name="numero_cartao"]')
            .mask("9999 9999 9999 9??? ????", optional);
}
module.exports.callback = ($) => {
    require('bootstrap/js/dist/util.js');
    require('bootstrap/js/dist/collapse.js');
    require('jquery-mask-plugin');
    require('jquery-validation');
    require('jquery-validation/dist/additional-methods');
    require('jquery-validation/dist/localization/messages_pt_BR');
    var ctx = $('div.step-checkout');

    var stepsContainer = $('.step-container');
    var initialized = false;
    function init() {
        if (initialized) {
            return;
        }
        initialized = true;
        var form = stepsContainer.find('.pagamento-cc-form');
        setMasks(form);
        setValidation(form);
    }

    stepsContainer.on('step:pre-exit:checkout', function (e) {
        $('.btn-continuar').addClass('btn-laranja')
                .attr('disabled', false);
    });
    stepsContainer.on('step:change:checkout', function (e) {
        $('.btn-continuar').removeClass('btn-laranja');
    });
};
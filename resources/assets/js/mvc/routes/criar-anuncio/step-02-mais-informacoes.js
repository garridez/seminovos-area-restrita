module.exports.seletor = '.c-criar-anuncio.a-index';

module.exports.callback = ($) => {
    $('.step-container').on('steps-loaded', init);
};

function init() {
    var ctx = $('.step-mais-informacoes');
    var observacoesTextarea = ctx.find('textarea[name="observacoes"]');
    var countSpan = ctx.find('.wordcount span.count');
    observacoesTextarea.keyup(function () {
        countSpan.html($(this).val().length);
    }).keyup();
}
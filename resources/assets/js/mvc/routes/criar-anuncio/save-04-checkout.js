module.exports.seletor = '.c-criar-anuncio.a-index';
function stopEvent(e) {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    return false;
}
module.exports.callback = ($) => {
    require('bootstrap/js/dist/util.js');
    require('bootstrap/js/dist/collapse.js');

    var stepsContainer = $('.step-container');

    stepsContainer.on('step:pre-exit:checkout', function (e) {
        $('.btn-continuar').addClass('btn-laranja');
    });
    stepsContainer.on('step:change:checkout', function (e) {
        $('.btn-continuar').removeClass('btn-laranja');
    });
};
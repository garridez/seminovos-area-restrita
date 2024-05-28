module.exports.seletor = '.c-criar-anuncio.a-index';

module.exports.callback = ($) => {
    require('bootstrap/js/dist/collapse.js');

    $('.step-container').on('steps-loaded', function () {
        $('.plano-box .input-radio-plano').change(function () {
            $('.btn-continuar').click();
        });
    });
};

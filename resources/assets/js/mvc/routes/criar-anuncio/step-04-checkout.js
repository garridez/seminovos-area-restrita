module.exports.seletor = '.c-criar-anuncio.a-index';

module.exports.callback = ($) => {
    $('.step-container')
            .on('step:change:checkout', function (e) {
                window.location = '#checkout';
            })
            .on('step:exit:checkout', function (e) {
                window.location = '#';
            });
};


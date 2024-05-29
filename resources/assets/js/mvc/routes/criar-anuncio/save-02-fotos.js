/**
 * Aqui é manipulado toda a parte de upload das imagens
 */
module.exports.seletor = '.c-criar-anuncio.a-index';

module.exports.callback = ($) => {
    require('./save-02-fotos-v2')($);
};

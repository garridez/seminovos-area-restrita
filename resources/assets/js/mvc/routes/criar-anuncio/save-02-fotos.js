/**
 * Aqui é manipulado toda a parte de upload das imagens
 */
export const seletor = '.c-criar-anuncio.a-index';

export const callback = ($) => {
    require('./save-02-fotos-v2')($);
};

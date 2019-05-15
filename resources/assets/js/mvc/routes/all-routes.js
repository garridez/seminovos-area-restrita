
module.exports.seletor = 'body';
module.exports.prepend = true; // Esse script precisa rodar primeiro
module.exports.callback = ($) => {
    require('components/Mask')();
};
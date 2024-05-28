/**
 *
 * Função comum para parar todos as formas de propagação de evento
 *
 * Exemplo:
 * var stopEvent = require('StopEvent');
 * $('.btn').click(function (e) {
 *  return stopEvent(e);
 * });
 *
 */

module.exports = function (event) {
    event.preventDefault();
    event.stopPropagation();
    event.stopImmediatePropagation();
    return false;
};

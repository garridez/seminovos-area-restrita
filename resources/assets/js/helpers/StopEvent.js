/**
 *
 * Função comum para parar todos as formas de propagação de evento
 *
 * Exemplo:
 * import stopEvent from 'StopEvent';
 * $('.btn').click(function (e) {
 *  return stopEvent(e);
 * });
 *
 */

export default function (event) {
    event.preventDefault();
    event.stopPropagation();
    event.stopImmediatePropagation();
    return false;
};

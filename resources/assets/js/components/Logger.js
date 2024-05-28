/**
 * Esse modulo serve para debuggar o js quando o console não está disponível (como por exemplo no mobile)
 *  Adiciona uma div acima de tudo e mostra o conteúdo das variáveis
 *
 * Modo de usar:
 *  var looger = require('components/Logger');
 *  var variavelQualquer = 'opa'
 *  looger(variavelQualquer); //  "opa"
 *  looger(variavelQualquer, 'Label'); // Label: "opa"
 */
var $ = require('jquery');
var div;
$(function () {
    div = $('<div>').addClass('debug-js').css({
        outline: '1px solid blue',
        position: 'fixed',
        zIndex: '99999',
        top: '0',
        left: '0',
        width: '100%',
        backgroundColor: 'rgba(255,255,255,0.8)',
        maxHeight: '500px',
        height: '100px',
        overflow: 'auto',
        whiteSpace: 'pre',
    });
    $('body').append(div);
});
module.exports = function (variable, label) {
    label = label || '';
    var b = JSON.stringify(variable, null, '\t');
    if (b && b.replace) {
        b = b.replace(/\\n/g, '\n');
    }

    if (b === undefined) {
        b = '{undefined}';
    }
    if (label !== '') {
        b = '<b>' + label + '</b>: ' + b;
    }
    div.prepend($('<div>').html(b));
    console.log(variable);
};

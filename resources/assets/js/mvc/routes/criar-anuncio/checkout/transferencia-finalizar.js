
/**
 * 
 * Finaliza o pagamento para o cliente enviar o comprovante depois
 */
module.exports = function () {
    var requestPagamento = require('./request-pagamento');
    $('a.pagamento-finalizar-deposito').click(function (e) {
        e.preventDefault();
        requestPagamento([{
                'name': 'metodo',
                'value': 'deposito'
            }]);
    });
};
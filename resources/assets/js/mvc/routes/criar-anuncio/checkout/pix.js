module.exports = function () {
    var requestPagamento = require('./request-pagamento');
    $('form.pagamento-pix-form').submit(function (e) {
        e.preventDefault();
        if (!$(this).valid()) {
            return;
        }
        requestPagamento([{
                'name': 'metodo',
                'value': 'pix'
            }]);
    });
};
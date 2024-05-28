/**
 * @todo Fazer essa função mostrar um alert com uma opção de cancelar a tentativa de pagamento anterior.
 * Essa função ainda deve ser criada na API
 */
module.exports = function () {
    var requestAlerts = require('./request-alerts');
    requestAlerts.erro('Existe uma transação em andamento! Aguarde');
};

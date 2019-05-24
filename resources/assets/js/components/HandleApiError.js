function showError(body, title, time) {
    var Alert = require('components/Alerts');
    Alert.error(body, title, time);

}
/**
 * A função verifica a resposta da API está ok
 *  Se não estiver OK, a função retona FALSE e mostra um alerta com a msg da API
 *  Se estiver OK, a função retorna TRUE e não faz mais nada
 * @param obj apiResponse resposta da API
 * @param int time Tempo de exibição do alert em milissegundos
 * @returns {Boolean}
 */
module.exports = function (apiResponse, time) {
    time = time || 15000;
    if (typeof apiResponse === 'object' && apiResponse.status === 200) {
        return true;
    }
    if (!apiResponse) {
        apiResponse = [];
    }
    var title = 'Houve um problema...';
    var body = '';
    if (apiResponse.title) {
        title = apiResponse.title;
    }
    if (apiResponse.detail) {
        body += apiResponse.detail;
    } else {
        body += 'Houve um problema ao processar sua solicitação.<br>Tente novamente.';

    }
    var messages = apiResponse.messages;
    if (messages) {
        body += '<br>';
        if (Array.isArray(messages)) {
            body += messages.join('<br>');
            /**
             * Quanto mais mensagens tiver, por mais tempo a mensagem será exibida
             */
            time += messages.length * 1000;
        } else {
            body += messages;
        }
    }
    showError(body, title, time);
    return false;

};
function showError(body, title, time) {
    var Alert = require('components/Alerts');
    Alert.error(body, title, time);

}
module.exports = function (apiResponse, time) {
    time = time || 15000;
    if (!apiResponse) {
        showError('Tivemos um problema ao processar sua solicitação.<br>Tente novamente.', 'Houve um problema...', time);
        return;
    }
    var body = '';

    if (apiResponse.detail) {
        body += apiResponse.detail;
    }
    var messages = apiResponse.messages;
    if (messages) {
        body += '<br>';
        if (Array.isArray(messages)) {
            body += messages.join('<br>');
            /**
             * Quanto mais mensagens tiver, por mais tempo a mensagem será exibida
             */
            time += messages.length *1000;
        } else {
            body += messages;
        }
    }
    showError(body, apiResponse.title, time);

};
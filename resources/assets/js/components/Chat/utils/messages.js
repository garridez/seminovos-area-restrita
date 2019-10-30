import moment from 'moment';


export function createNewMessage(idConversa, idCadastroRemetente, mensagem) {
    const idChatMensagem = Math.random().toString(36).substr(2, 9);
    const enviadoEm = moment().format('YYYY-MM-DD HH:mm:ss.SSSS');
    const lidoEm = null;
    const delivered = false;

    return {
        idChatMensagem,
        idConversa,
        idCadastroRemetente,
        mensagem,
        enviadoEm,
        lidoEm,
        delivered
    };
}


export function sendNewMessage(message, callback) {
    var messageCopy = {...message};
    var data = {
        'idConversa': messageCopy.idConversa,
        'idCadastroRemetente': messageCopy.idCadastroRemetente,
        'mensagem': messageCopy.mensagem,
    };
    console.log(data);
    return;
    $.ajax({
        url: '/chat/mensagens',
        data: data,
        type: 'POST',
        dataType: 'json',
        success: function (data) {
            if (typeof callback === 'function') {
                callback(data.data);
            }
        },
        error: function (e) {
            console.log('enviar msg');
        }
    });
}
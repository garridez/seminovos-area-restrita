import io from 'socket.io-client';

const websocket = io('/', {
    path: '/chat/websocket/',
    transports: ['websocket', 'polling']
});
websocket.on('connect', function () {
    console.log('connected');

});
websocket.on('list-mensagens', function () {
    return;
    // Esse código abaixo é para testar quando estiver desenvolvendo
    setTimeout(function () {
        $('.list-chats li').eq(1).click();
        setTimeout(function () {
            $('.conversation-options').click();
            setTimeout(function () {
                $('.dropdown-menu.show a').first()[0].click();
                setTimeout(function () {
                    //$('.modal-footer .btn.btn-primary').first()[0].click();
                }, 500);
            }, 500);
        }, 500);
    }, 2000);
});

export default (state = websocket, action) => {

    switch (action.type) {
        case 'CHAT_MESSAGE_READED':
            websocket.emit('msg-readed', action.message);
            return state;
            break;
        case 'CHAT_DELETE_CONVERSA':

            websocket.emit('msg-delete-conversa', action.data.idConversa);
            return state;
            break;
        case 'CHAT_SEND_MESSAGE':
            console.log('.');

//            setTimeout(function () {
//                message.delivered = true;
//                message.idChatMensagem = 321;
//                console.log('delivered');
//                console.log(message);
//            }, 1000);
            //console.log(message);
            websocket.emit('msg-send', action.message);
            return {
                ...state
            };
            break;

        default:

            break;
    }
    return state;
};



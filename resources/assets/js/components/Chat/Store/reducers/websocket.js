import io from 'socket.io-client';

const websocket = io('/', {
    path: '/chat/websocket/'
});
websocket.on('connect', function () {
    console.log('connected');
});
websocket.on('list-mensagens', function () {
    $(window).resize();
});

export default (state = websocket, action) => {

    switch (action.type) {
        case 'CHAT_MESSAGE_READED':
            websocket.emit('msg-readed', action.message);
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



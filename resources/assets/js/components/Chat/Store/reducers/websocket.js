import io from 'socket.io-client';

const websocket = io('/', {
    path: '/chat/websocket/'
});
websocket.on('connect', function () {
    console.log('connected');
});
websocket.on('initial-messages', function () {
    console.log('initial-messages');
    setTimeout(function () {
        $('.chat-subject').eq(0).click();
        setTimeout(function () {
            $('.editor form input').val(Math.random().toString(36).substr(2, 9));
            $('.editor form button').click();
        }, 100);
    }, 500);
});

export default (state = websocket, action) => {

    switch (action.type) {
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



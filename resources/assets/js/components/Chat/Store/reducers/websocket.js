import io from 'socket.io-client';

const websocket = io('/', {
    path: '/chat/websocket/'
});
websocket.on('connect', function () {
    console.log('connected');
});

export default (state = websocket, action) => {
    return state;
};



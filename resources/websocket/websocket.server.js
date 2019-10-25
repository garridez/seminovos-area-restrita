import helpers from './helpers';
import cookie from 'cookie';

import SocketIO from 'socket.io';
import MessagesGateway from './SnBH/MessagesGateway';

const activeConnections = [];

function initServer(apiClient) {
    const io = SocketIO({
        path: '/chat/websocket/'
    });

    io.sockets.on('connection', async function (socket) {
        console.log('A client is connected. Id:', socket.id);
        addConnection(socket.id);

        socket.on('disconnect', function () {
            removeConnection(socket.id);
            messagesGateway = null;
        });

        var cookies = cookie.parse(socket.handshake.headers.cookie);
        var idCookie = cookies.PHPSESSID;
        var messagesGateway;

        try {
            var idCadastro = await helpers.getIdCadastroBySession(idCookie);
            if (idCadastro) {
                messagesGateway = new MessagesGateway(idCadastro, socket, apiClient);
            } else {
                console.log('idCadastro não encontrado');
            }
        } catch (e) {
            console.log('Sessão não encontrada. idCookie: ', idCookie);
            console.log(e);
        }
    });

    return io;
}

export default function (httpServer, apiClient) {
    let io = initServer(apiClient);
    io.listen(httpServer);
    return io;
};

function addConnection(socketId) {
    activeConnections.push(socketId);
    console.log('Total Clients connecteds:', activeConnections.length);
}

function removeConnection(socketId) {
    activeConnections.splice(activeConnections.indexOf(socketId));
    console.log('Total Clients connecteds:', activeConnections.length);
}
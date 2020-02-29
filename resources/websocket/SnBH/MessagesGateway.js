

class MessagesGateway {
    idCadastro;
    socket;
    apiClient;
    socketId;
    idLastMessage = 0;
    messageLoaderTimeoutId = false;
    constructor(idCadastro, socket, apiClient) {
        this.idCadastro = idCadastro;
        this.socket = socket;
        this.apiClient = apiClient;
        this.socketId = socket.id;
        this.setEvents();
        this.sendInitialMessages();
    }
    on(event, callback) {
        this.socket.on(event, callback);
    }
    emit(event, data) {
        console.log('Emit event', event)
        this.socket.emit(event, data || null);
    }
    setEvents() {
        for (let event in this.events) {
            this.on(event, this.events[event].bind(this));
        }
    }
    time(label) {
        return; // Remover só para debuggar em dev
        console.log(this.idCadastro + this.socketId + label + ' --- START');

    }
    timeEnd(label) {
        return; // Remover só para debuggar em dev
        console.log(this.idCadastro + this.socketId + label + ' --- END');
    }
    async getConversas() {
        try {
            this.time('MessagesGateway:getConversas');
            var {data} = await this.apiClient.conversasGet(null, this.idCadastro);
            this.timeEnd('MessagesGateway:getConversas');
            if (0) {
                // Debugar as requisições
                console.log(
                        this.socketId,
                        'Novas mensagens carregadas ',
                        data.idLastMessage,
                        Object.keys(data.listChats || {}).length
                        );
            }

            return data;
        } catch (e) {
            console.log('pau no ajax getConversas');
            console.log(e);
            return [];
        }
    }
    async getMessages(lastMessage = true) {
        var params = {
            idCadastro: this.idCadastro,
            method: ['listMensagens', 'listLidas']
        };
        if (lastMessage && this.idLastMessage) {
            params.maiorQue = this.idLastMessage;
        }

        try {
            var {data} = await this.apiClient.mensagensGet(params);
            if (0) {
                // Debugar as requisições
                console.log(
                        this.socketId,
                        'Novas mensagens carregadas ',
                        data.idLastMessage,
                        Object.keys(data.listMensagens || {}).length,
                        params
                        );
            }

            this.idLastMessage = data.idLastMessage || 0;
            return data;
        } catch (e) {
            console.log('pau no ajax getMessages');
            console.log('mensagensGet', params);
            console.log(e);
            return [];
    }

    }
    async messageReaded(msg) {
        try {
            await this.apiClient.mensagensPatch({
                lido: 1
            }, msg.idChatMensagem);
        } catch (e) {
            console.log(e);
        }
    }
    async messageSender(msg) {
        var result = await this.apiClient.mensagensPost(msg);
        this.idLastMessage = result.data.idChatMensagem;

        var data = result.data;
        data.idChatMensagemTemp = msg.idChatMensagem;

        return data;
    }

    messagesLoaderTimeoutId;

    async messagesLoader() {
        try {
            this.time('MessagesGateway:messagesLoader');
            var data = await this.getMessages();
            var {listMensagens, listLidas} = data;
            this.timeEnd('MessagesGateway:messagesLoader');
        } catch (e) {
            console.log('Messages loader error');
            console.log(e);
            return;
        }

        if (Object.keys(listMensagens || {}).length !== 0) {
            console.log('Nova mensagem');
            this.emit('mensagem', listMensagens);
        }

        if (Object.keys(listLidas || {}).length !== 0) {
            this.emit('mgs-lidas', listLidas);
        }

        if (!this.socket.connected) {
            return;
        }
        if (this.messagesLoaderTimeoutId) {
            return;
        }

        this.messagesLoaderTimeoutId = setTimeout(() => {
            this.messagesLoaderTimeoutId = false;
            this.messagesLoader();
        }, 2000);
    }
}

MessagesGateway.prototype.events = {
    'list-chats': async function () {
        try {
            this.time('MessagesGateway:list-chats');
            var conversas = await this.getConversas();
            this.emit('list-chats', conversas);
            this.messagesLoader(); // Interval
            this.timeEnd('MessagesGateway:list-chats');
        } catch (e) {
            console.log('pau no ajax list-chats');
            console.log(e);
        }
    },
    'list-mensagens': async function () {
        try {
            this.time('MessagesGateway:list-mensagens');
            var data = await this.getMessages(false);
            this.emit('list-mensagens', data.listMensagens);
            this.timeEnd('MessagesGateway:list-mensagens');
        } catch (e) {
            console.log('pau no ajax list-mensagens');
            console.log(e);
        }
    },
    'initial-messages': async function () {
        try {
            var data = await this.getMessages(false);
            this.emit('initial-messages', data.listMensagens);
            this.messagesLoader();
        } catch (e) {
            console.log('pau no ajax initial-messages');
            console.log(e);
        }
    },
    'msg-send': async function (msg) {
        this.time('MessagesGateway:msg-send');
        var result = await this.messageSender(msg);
        this.timeEnd('MessagesGateway:msg-send');
        this.emit('msg-delivered', result);
    },
    'msg-readed': async function (msg) {
        this.messageReaded(msg);
    }
};
MessagesGateway.prototype.sendInitialMessages = async function () {
    await this.events['list-chats'].call(this);
    this.events['list-mensagens'].call(this);
    //this.events['initial-messages'].call(this);
};
export default MessagesGateway;
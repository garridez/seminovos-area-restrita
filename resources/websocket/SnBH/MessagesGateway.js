

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
        this.socket.emit(event, data || null);
    }
    setEvents() {
        for (let event in this.events) {
            this.on(event, this.events[event].bind(this));
        }
    }
    async getConversas() {
        try {
            var {data} = await this.apiClient.conversasGet(null, this.idCadastro);
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
            console.log('pau no ajax');
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
            console.log('pau no ajax');
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
    async messagesLoader() {
        try {
            var data = await this.getMessages();
            var {listMensagens, listLidas} = data;
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

        setTimeout(() => {
            this.messagesLoader();
        }, 2000);
    }
}

MessagesGateway.prototype.events = {
    'list-chats': async function () {
        try {
            var conversas = await this.getConversas();
            this.emit('list-chats', conversas);
            this.messagesLoader(); // Interval
        } catch (e) {
            console.log('pau no ajax');
            console.log(e);
        }
    },
    'list-mensagens': async function () {
        try {
            var data = await this.getMessages(false);
            this.emit('list-mensagens', data.listMensagens);
            this.messagesLoader();
        } catch (e) {
            console.log('pau no ajax');
            console.log(e);
        }
    },
    'initial-messages': async function () {
        try {
            var data = await this.getMessages(false);
            this.emit('initial-messages', data.listMensagens);
            this.messagesLoader();
        } catch (e) {
            console.log('pau no ajax');
            console.log(e);
        }
    },
    'msg-send': async function (msg) {
        var result = await this.messageSender(msg);
        this.emit('msg-delivered', result);
    },
    'msg-readed': async function (msg) {
        this.messageReaded(msg);
    },
};
MessagesGateway.prototype.sendInitialMessages = function () {
    this.events['list-chats'].call(this);
    this.events['list-mensagens'].call(this);
    //this.events['initial-messages'].call(this);
};
export default MessagesGateway;
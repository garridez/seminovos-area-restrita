import _ from 'lodash';
import {sendNewMessage} from '../../utils/messages';


/**
 * 
 */
const messagesSent = {};

const messages = (state = false, action) => {
    switch (action.type) {
        case 'LIST_CHAT_REFRESH_STATE':
            return {
                ...state
            };
        case 'LIST_CHAT_NEW_MSG':
            var message = action.message;
            var {idConversa} = message;

            var chatData = state[idConversa];
            var totalMensagens = parseInt(chatData.totalMensagens);
            totalMensagens++;
            chatData.totalMensagens = '' + totalMensagens;

            state[idConversa] = chatData;

//            sendNewMessage(message, (data) => {
//                var idChatMensagem = data.idChatMensagem;
//                message.idChatMensagem = idChatMensagem;
//                message.delivered = true;
//            });

            return {
                ...state,
            };
        case 'LIST_CHAT_DELETE':
            var idConversa = action.data.idConversa;
            delete state[idConversa];
            return {
                ...state
            };
        case 'LIST_CHAT_UPDATE_LAST_MSG':
            var hasChange = false;
            const {listChats, listMensagens} = action;
            // Atualiza a referência de última msg enviada
            _.forEach(action.listChats, (chatData, idConversa) => {
                const listMsgs = listMensagens[idConversa];
                if (listMsgs) {
                    let lastMsgId = _.findLastKey(listMsgs);
                    if (lastMsgId !== chatData.lastMessage.idChatMensagem) {
                        chatData.lastMessage = listMsgs[lastMsgId];
                        hasChange = true;
                    }
                }

                state[idConversa] = chatData;
            });
            if (!hasChange) {
                return state;
            }
            return {
                ...state
            };
        case 'LIST_CHAT_LOAD':
            if (state === false) {
                state = {};
            }

            _.forEach(action.listChats, (chatData, idConversa) => {
                state[idConversa] = chatData;
            });

            return {
                ...state
            };
        default:
            return state;
}
};

export default messages;

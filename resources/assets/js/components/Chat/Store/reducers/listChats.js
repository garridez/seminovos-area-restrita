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
            chatData.mensagens.unshift(message);
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

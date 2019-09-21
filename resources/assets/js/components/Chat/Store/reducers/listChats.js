import _ from 'lodash';
import {createNewMessage} from '../../utils/messages';

const messages = (state = {}, action) => {
    switch (action.type) {
        case 'LIST_CHAT_NEW_MSG':
            const message = action.message.toObject();
            const {idConversa} = message;

            var chatData = state[idConversa];
            chatData.mensagens.unshift(message);
            var totalMensagens = parseInt(chatData.totalMensagens);
            totalMensagens++;
            chatData.totalMensagens = '' + totalMensagens;

            state[idConversa] = chatData;

            return {
                ...state,
            };
        case 'LIST_CHAT_LOAD':
            
            _.forEach(action.listChats, (chatData, idConversa) => {
                if (state[idConversa]) {
                    var prevChatData = state[idConversa];
                    chatData.mensagens = chatData.mensagens.concat(prevChatData.mensagens);
                    state[idConversa] = chatData;
                } else {
                    state[idConversa] = chatData;
                }

            });
            var listChats = {
                ...state
            };
            return listChats;
        default:
            return state;
}
};

export default messages;

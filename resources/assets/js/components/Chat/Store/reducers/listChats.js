
import {createNewMessage} from '../../utils/messages';

const messages = (state = {}, action) => {
    switch (action.type) {
        case 'LIST_CHAT_NEW_MSG':
            console.log('add new msg');
            const message = action.message.toObject();
            const {idConversa} = message;

            console.log(message);
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
            var listChats = {
                ...state,
                ...action.listChats
            };
            return listChats;
        default:
            return state;
}
};

export default messages;

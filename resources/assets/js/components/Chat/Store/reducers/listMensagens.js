import _ from 'lodash';
import {sendNewMessage} from '../../utils/messages';

const messages = (state = {}, action) => {
    switch (action.type) {
        case 'LIST_MENSAGENS':
            _.each(action.listMensagens, (msgs, key) => {
                state[key] = state[key] || {};
                _.each(msgs, (msg, keyMsg) => {
                    state[key][keyMsg] = msg;
                });
            });
            return {...state};
        case 'LIST_CHAT_NEW_MSG':
            const {message} = action;
            var messages = state[message.idConversa];

            messages[message.idChatMensagem] = message;
            return {...state};
        case 'LIST_MENSAGENS_DELIVERED':
            const {idConversa, idChatMensagem, idChatMensagemTemp} = action.data;
            var messages = state[idConversa];

            // Substitui a key da msg pelo id definitivo
            Object.defineProperty(messages, idChatMensagem,
                    Object.getOwnPropertyDescriptor(messages, idChatMensagemTemp));
            delete messages[idChatMensagemTemp];
            messages[idChatMensagem].delivered = true;

            return {...state};
        default:
            return state;
}
};

export default messages;

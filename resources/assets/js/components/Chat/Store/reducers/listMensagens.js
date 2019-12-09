import _ from 'lodash';
import {sendNewMessage} from '../../utils/messages';


/**
 * 
 */
const messagesSent = {};

const messages = (state = {}, action) => {
    switch (action.type) {
        case 'LIST_MENSAGENS':
            return {
                ...action.listMensagens
            };
        default:
            return state;
}
};

export default messages;

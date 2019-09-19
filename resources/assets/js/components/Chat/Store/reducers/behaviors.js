import {createNewMessage} from '../../utils/messages';

const todos = (state = {}, action) => {
    switch (action.type) {
        case 'CHAT____ACTIVE':
            return {
                ...state,
                conversationActive: action.idConversa
            };
        default:
            return state;
}
};

export default todos

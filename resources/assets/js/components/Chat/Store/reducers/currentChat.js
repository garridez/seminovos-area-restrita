
export default (state = {}, action) => {
    switch (action.type) {
        case 'CHAT_ACTIVE':
            return {
                ...state,
                conversationActive: action.data.idConversa,
            };
        case 'CHAT_SEND_MESSAGE':
            return {
                ...state,
            };
        default:
            return state;
}
};



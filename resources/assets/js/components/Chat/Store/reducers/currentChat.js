const initalState = {
    conversationActive: null,
    idLastMessage: null
};
export default (state = initalState, action) => {
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
        case 'CHAT_LAST_ID_MESSAGE':
            const {idLastMessage} = action;
            return {
                ...state,
                idLastMessage
            };
        default:
            return state;
}
};



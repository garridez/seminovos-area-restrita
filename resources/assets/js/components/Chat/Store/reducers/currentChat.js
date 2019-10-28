const initalState = {
    conversationActive: null
};
export default (state = initalState, action) => {
    switch (action.type) {
        case 'CHAT_ACTIVE':
            return {
                ...state,
                conversationActive: action.data.idConversa
            };
        case 'CHAT_SEND_MESSAGE':
            return {
                ...state
            };
        default:
            return state;
}
};



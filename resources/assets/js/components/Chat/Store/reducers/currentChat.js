const initalState = {
    conversationActive: null
};
export default (state = initalState, action) => {

    if (action.type === 'CHAT_ACTIVE') {
        return {
            ...state,
            conversationActive: action.data.idConversa
        };
    }

    return state;
};



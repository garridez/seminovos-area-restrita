
export default (state = {}, action) => {
    switch (action.type) {
        case 'LIST_CHAT_FILTER':
            var text = action.text;
            if (typeof text === 'string') {
                text = text.trim();
            }
            return {
                ...state,
                text
            };
        default:
            return state;
}
};



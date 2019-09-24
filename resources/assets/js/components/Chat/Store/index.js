import { createStore, combineReducers, applyMiddleware } from 'redux';
import $ from 'jquery';

import behaviors from './reducers/behaviors';
import listChats from './reducers/listChats';
import cadastro from './reducers/cadastro';
import currentChat from './reducers/currentChat';
import filter from './reducers/filter';

const reducer = combineReducers({
    behaviors,
    listChats,
    cadastro,
    currentChat,
    filter
});

const middle = (store) => {
    return next => action => {
            if (action.type !== 'CHAT_SEND_MESSAGE') {
                return next(action);
            }
            const returnValue = next(action);
            store.dispatch({
                'type': 'LIST_CHAT_NEW_MSG',
                message: returnValue.message
            });

            return returnValue;
        };
};

export default createStore(reducer, applyMiddleware(middle));
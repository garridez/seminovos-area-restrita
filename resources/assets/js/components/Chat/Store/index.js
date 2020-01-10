import { createStore, combineReducers, applyMiddleware } from 'redux';
import $ from 'jquery';

import behaviors from './reducers/behaviors';
import listChats from './reducers/listChats';
import listMensagens from './reducers/listMensagens';
import cadastro from './reducers/cadastro';
import currentChat from './reducers/currentChat';
import filter from './reducers/filter';
import websocket from './reducers/websocket';

const reducer = combineReducers({
    behaviors,
    listChats,
    listMensagens,
    cadastro,
    currentChat,
    filter,
    websocket
});

const middle = (store) => {
    return next => action => {
            
            if (action.type !== 'CHAT_SEND_MESSAGE') {
                return next(action);
            }
            const returnValue = next(action);
            store.dispatch({
                'type': 'LIST_CHAT_NEW_MSG',
                message: action.message
            });
            const {listChats, listMensagens} = store.getState();
            store.dispatch({
                'type': 'LIST_CHAT_UPDATE_LAST_MSG',
                listChats,
                listMensagens
            });

            return returnValue;
        };
};

export default createStore(reducer, applyMiddleware(middle));
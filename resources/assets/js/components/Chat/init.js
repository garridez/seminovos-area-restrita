
import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import Chat from 'components/Chat';
import store from './Store';


export default function () {
    return ReactDOM.render(
            <Provider store={store}>
                <Chat baseUrl=""/>
            </Provider>,
            document.querySelector('.chat-root')
            );
}
 
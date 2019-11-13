
import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import Chat from '../Chat/Index';
import MsgHandler from './Controllers/MsgHandler';
import store from './Store';

window.setAjaxLoadding = false;

export default function (chatRoot) {
    return ReactDOM.render(
            <Provider store={store}>
                <MsgHandler />
                <Chat />
            </Provider>,
            chatRoot.get(0)
            )
}

var onResize = function () {
    $('.section-chat > *').css('height', window.innerHeight - $('.header').outerHeight());
};
$(window).resize(onResize);
setTimeout(onResize, 1000);
 
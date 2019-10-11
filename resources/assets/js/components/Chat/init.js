
import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';

import Chat from '../Chat/Index';
import store from './Store';

window.setAjaxLoadding = false;

export default function () {
    return ReactDOM.render(
            <Provider store={store}>
                <Chat />
            </Provider>,
            document.querySelector('.chat-root')
            );
}

var onResize = function () {
    $('.section-chat > *').css('height', window.innerHeight - $('.header').outerHeight());
};
$(window).resize(onResize);
onResize();

 

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

$(window).resize(function () {
    console.log('resize');
    var headerHeight = $('.header').outerHeight();
    var $mainChat = $('.section-chat .main-chat');
    var $topHeader = $mainChat.find('> .top-header');
    var $conversation = $mainChat.find('> .conversation');
    var $editor = $mainChat.find('> .editor');
    var innerHeight = window.innerHeight;
    console.log({innerHeight})
    if (window.visualViewport && window.visualViewport.height) {
        innerHeight = window.visualViewport.height;
    }
    console.log({innerHeight})

    var conversationHeight = innerHeight - headerHeight - $editor.outerHeight() - $topHeader.outerHeight() - 15;

    $conversation.height(conversationHeight);


    $editor.width($mainChat.width());


    // $('.section-chat > *').css('height', window.innerHeight - $('.header').outerHeight());
}).resize();
$('.chat-subject.chat').eq(0).click();
setTimeout(function () {
  $('.editor input').focus()
  $('.chat-subject.chat').eq(0).click();
}, 1000);
setTimeout(function () {
  $('.chat-subject.chat').eq(0).click();
}, 3000);


import React, { Component, PropTypes } from 'react';
import ReactDOM from 'react-dom';
import ListChats from './ListChats';
import Conversation from './Conversation';
import Editor from './Editor';
import $ from 'jquery';
import data from './data';

window.React = React;
window.ReactDOM = ReactDOM;
//var chat = <section className="section-chat">
export default class Chat extends Component {

    constructor(props) {
        super(props)
        this.state = {
            conversationActive: null,
            listChats: data
        };
        this.loadConversations();
        this.activeConversation = this.activeConversation.bind(this);

    }
    getUrl(type) {
        return this.props['baseUrl'] + (this.props[type] || '');
    }
    loadConversations() {
        let url = this.getUrl('urlMensagens');

        $.getJSON(url, (listChats) => {
            this.setState((prevState, props) => {
                var newState = {};
                newState.listChats = _.mergeWith(listChats, prevState.listChats, function (objValue, srcValue, key) {
                    /**
                     * @todo merge das msgs
                     */
                });
                return newState;
            })
        })
    }
    activeConversation(id) {
        console.log('Ativar esse cara aqui: ', id);
        this.setState({
            conversationActive: id,
            currentConversation: this.state.listChats[id],
        });

    }
    render() {
        const {listChats} = this.state;
        const {conversationActive} = this.state;
        var mensagens = {};
        var conversation = {};
        if (this.state.currentConversation) {
            conversation = this.state.currentConversation || {mensagens: {}};
            mensagens = conversation.mensagens;
        }

        return (
                <section className="section-chat row">
                    <div className="col-md-3">
                        <ListChats
                            listChats={listChats}
                            conversationActive={conversationActive}
                            onActive={this.activeConversation} />
                    </div>
                    <div className="conversation-container col-md-9" >
                        <Conversation conversation={conversation} mensagens={mensagens}/>
                        <Editor/>
                    </div>
                </section>
                );
    }
}
Chat.defaultProps = {
    urlMensagens: '/chat/mensagens'
};
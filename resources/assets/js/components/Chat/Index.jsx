
import React, { Component, PropTypes } from 'react';
import ReactDOM from 'react-dom';
import ListChats from './ListChats';
import Conversation from './Conversation';
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

    handleFilterUpdate(filter) {
        ///this.setState({filter})
//        console.log(filter)
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
        })
    }
    render() {
        const {listChats} = this.state;
        const {conversationActive} = this.state;
        return (
                <section className="section-chat">
                    <div>
                        <ListChats
                            listChats={listChats}
                            conversationActive={conversationActive}
                            onActive={this.activeConversation} />
                    </div>
                    <div>
                        <Conversation />
                    </div>
                </section>
                );
    }
}
Chat.defaultProps = {
    urlMensagens: '/chat/mensagens'
};
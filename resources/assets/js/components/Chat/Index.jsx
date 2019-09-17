
import React, { Component, PropTypes } from 'react';
import ReactDOM from 'react-dom';
import Profile from './Sidebar/Profile';
import Filter from './Sidebar/Filter';
import ListChats from './Sidebar/ListChats';
import History from './Conversation/History';
import Editor from './Conversation/Editor';
import Contact from './Conversation/Contact';
import $ from 'jquery';
import data from './data';

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
                    <div className="sidebar col-md-5 d-flex flex-column">
                        <Profile />
                        <Filter />
                        <ListChats
                            listChats={listChats}
                            conversationActive={conversationActive}
                            onActive={this.activeConversation} />
                    </div>
                    <div className="main-chat col-md-7 d-flex flex-column">
                        <Contact />
                        <div className="conversation-container">
                            <History conversation={conversation} mensagens={mensagens}/>
                        </div>
                        <Editor className=""/> 
                    </div>
                </section>
                );
    }
}
Chat.defaultProps = {
    urlMensagens: '/chat/mensagens'
};
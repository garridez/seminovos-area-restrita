import React, { Component, PropTypes } from 'react';
import ReactDOM from 'react-dom';
import { connect } from 'react-redux';
import _ from 'lodash';

import Profile from './Sidebar/Profile';
import Filter from './Sidebar/Filter';
import ListChats from './Sidebar/ListChats';
import History from './Conversation/History';
import Editor from './Conversation/Editor';
import Contact from './Conversation/Contact';
import MsgLoader from './Controller/MsgLoader';

class Chat extends Component {

    constructor(props) {
        super(props);
        this.state = {
            conversationActive: null
        };

        this.activeConversation = this.activeConversation.bind(this);
    }

    activeConversation(id) {
        console.log('NÃO É PRA EU RODAR');
        this.setState({
            conversationActive: id,
            currentConversation: this.props.listChats[id],
        });

    }
    render() {
        const {
            listChats,
            conversationActive,
            currentConversation
        } = this.props;

        return (
                <section className="section-chat row">
                    <MsgLoader/>
                    <div className="sidebar col-sm-5 d-flex flex-column">
                        <div className="top-header">
                            <Profile />
                            <Filter />
                        </div>
                        <ListChats
                            listChats={listChats}
                            conversationActive={conversationActive}
                            onActive={this.activeConversation} />
                    </div>
                    <div className="main-chat col-sm-7 d-flex flex-column">
                        <div className="top-header">
                            <Contact />
                        </div>
                        <History conversation={currentConversation} />
                        <Editor /> 
                    </div>
                </section>
                );
    }
}

export default connect((state, ownProps) => {

    var listChats = state.listChats;
    var conversationActive = state.currentChat.conversationActive;
    var currentConversation = null;

    if (conversationActive && listChats[conversationActive]) {
        currentConversation = listChats[conversationActive];
    }

    return {
        listChats,
        conversationActive,
        currentConversation
    };

})(Chat);
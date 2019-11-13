import React, { Component, PropTypes } from 'react';
import ReactDOM from 'react-dom';
import { connect } from 'react-redux';
import _ from 'lodash';

// Components Sidebar
import Filter from '../Components/Sidebar/Filter';
import ListChats from '../Components/Sidebar/ListChats';
import Profile from '../Components/Sidebar/Profile';

// Components Conversation
import Contact from '../Components/Conversation/Contact';
import Editor from '../Components/Conversation/Editor';
import History from '../Components/Conversation/History';

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


        var conversationActiveClass = conversationActive ? 'conversation-active' : '';

        return (
                <section className={'section-chat row ' + conversationActiveClass}>
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
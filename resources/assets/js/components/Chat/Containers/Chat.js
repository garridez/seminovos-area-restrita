import React, { Component, PropTypes } from 'react';
import ReactDOM from 'react-dom';
import { connect } from 'react-redux';

// Components Sidebar
import Filter from '../Components/Sidebar/Filter';
import ListChats from '../Components/Sidebar/ListChats';
import Profile from '../Components/Sidebar/Profile';

// Components Conversation
import Contact from '../Components/Conversation/Contact';
import Editor from '../Components/Conversation/Editor';
import History from '../Components/Conversation/History';

class Chat extends Component {

    render() {
        const {
            conversationActive
        } = this.props;

        const conversationActiveClass = conversationActive ? 'conversation-active' : '';

        return (
                <section className={'section-chat row ' + conversationActiveClass}>
                    <div className="sidebar col-sm-4 d-flex flex-wrap">
                        <div className="top-header">
                            <Profile />
                            <Filter />
                        </div>
                        <ListChats conversationActive={conversationActive} />
                    </div>
                    <div className="main-chat col-sm-8 d-flex flex-wrap">
                        <div className="top-header">
                            <Contact />
                        </div>
                        <History/>
                        <Editor /> 
                    </div>
                </section>
                );
    }
}

export default connect((state) => {
    return {
        conversationActive: state.currentChat.conversationActive
    };
})(Chat);
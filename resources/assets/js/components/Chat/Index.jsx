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
import $ from 'jquery';
import data from './data';

class Chat extends Component {

    constructor(props) {
        super(props);
        this.state = {
            conversationActive: null
        };
        
        this.activeConversation = this.activeConversation.bind(this);

        /**
         var cvsk = Object.keys(data);
         var pt1 = {};
         var pt2 = {};
         _.map(cvsk.slice(0, 5), function (k) {
         pt1[k] = data[k];
         });
         _.map(cvsk.slice(5), function (k) {
         pt2[k] = data[k];
         });
         
         
         this.props.dispatch({
         type: 'LIST_CHAT_LOAD',
         listChats: pt1,
         });
         
         this.props.dispatch({
         type: 'CADASTRO_SET_DATA',
         data: Object.values(data)[0]
         });
         
         setTimeout(() => {
         //            console.log()
         this.props.dispatch({
         type: 'LIST_CHAT_LOAD',
         listChats: pt2,
         });
         }, 2000);*/


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
            currentConversation,
            currentMessages
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
    var currentMessages = null;
    var meuIdCadastro = null;
    if (listChats) {
        var firstKey = _.keys(listChats)[0];
        if (firstKey) {
            meuIdCadastro = listChats[firstKey].meuIdCadastro;
        }
    }

    if (conversationActive && listChats[conversationActive]) {
        currentConversation = listChats[conversationActive];
        currentMessages = currentConversation.mensagens;
    }

    return {
        listChats,
        conversationActive,
        currentConversation,
        currentMessages,
        meuIdCadastro
    };

})(Chat);
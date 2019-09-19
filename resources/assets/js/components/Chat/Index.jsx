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
import $ from 'jquery';
import data from './data';

class Chat extends Component {

    constructor(props) {
        super(props);
        this.state = {
            conversationActive: null
        };
        this.loadConversations();
        this.activeConversation = this.activeConversation.bind(this);


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
        }, 2000);


    }
    getUrl(type) {
        return this.props['baseUrl'] + (this.props[type] || '');
    }
    loadConversations() {
        let url = this.getUrl('urlMensagens');
        return;
        $.getJSON(url, (listChats) => {

            this.setState((prevState, props) => {

                return {};
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
                        <History conversation={currentConversation} />
                        <Editor /> 
                    </div>
                </section>
                );
    }
}
Chat.defaultProps = {
    urlMensagens: '/chat/mensagens'
};

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
    //console.log(currentMessages);

    return {
        listChats,
        conversationActive,
        currentConversation,
        currentMessages,
        meuIdCadastro
    };

})(Chat);
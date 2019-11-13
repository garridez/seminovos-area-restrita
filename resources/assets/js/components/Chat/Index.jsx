import React, { Component, PropTypes } from 'react';
import ReactDOM from 'react-dom';
import { connect } from 'react-redux';
import _ from 'lodash';

import Loading from './Containers/Loading';


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

        if (listChats === false) {
            console.log('Sem msgs');
            return (<Loading />);
        } else {
            console.log('Com msgs');
        }

        return (<div>render chat</div>);
    }
}

export default connect((state, ownProps) => {

    var listChats = state.listChats;
    var conversationActive = state.currentChat.conversationActive;
    var currentConversation = null;

    if (conversationActive && listChats[conversationActive]) {
        currentConversation = listChats[conversationActive];
    }
    console.log(listChats);
    return {
        listChats,
        conversationActive,
        currentConversation
    };

})(Chat);
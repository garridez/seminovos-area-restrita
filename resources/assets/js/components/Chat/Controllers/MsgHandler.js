import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';
import _ from 'lodash';

class MsgLoader extends Component {

    constructor(props) {
        super(props);
        this.websocketEvents();
    }

    websocketEvents() {
        this.props.websocket
                .on('initial-messages', (listChats) => {
                    this.props.dispatch({
                        type: 'LIST_CHAT_LOAD',
                        listChats
                    });
                })
                .on('user-data', (userData) => {
                    this.props.dispatch({
                        type: 'CADASTRO_SET_DATA',
                        data: userData
                    });
                })
                .on('mensagem', (listChats) => {
                    console.log('nova mensagem');
                    console.log(listChats);
                })
                .on('msg-delivered', (data) => {
                    console.log('msg-delivered');
                    console.log(data);
                });
    }

    render() {
        return '';
    }
}

export default connect((state) => {
    const {websocket} = state;
    return {
        websocket
    };
})(MsgLoader);
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
                .on('list-chats', (listChats) => {
                    this.props.dispatch({
                        type: 'LIST_CHAT_LOAD',
                        listChats
                    });
                })
                .on('list-mensagens', (listMensagens) => {
                    this.props.dispatch({
                        type: 'LIST_MENSAGENS',
                        listMensagens
                    });
                })
                .on('mgs-lidas', (mgsLidas) => {
                    this.props.dispatch({
                        type: 'MSG_LIDAS',
                        mgsLidas
                    });
                })
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
                .on('mensagem', (listMensagens) => {
                    this.props.dispatch({
                        type: 'LIST_MENSAGENS',
                        listMensagens
                    });
                })
                .on('msg-delivered', (data) => {
                    this.props.dispatch({
                        type: 'LIST_MENSAGENS_DELIVERED',
                        data
                    });
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
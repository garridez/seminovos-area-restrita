import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';
import _ from 'lodash';
import io from 'socket.io-client';

class MsgLoader extends Component {

    constructor(props) {
        super(props);
        this.websocket();
    }

    websocket() {
        var socket = io('/', {
            path: '/chat/websocket/'
        });
        socket
                .on('connect', function () {
                    console.log('connectado');
                })
                .on('initial-messages', (listChats) => {
                    this.dispatchData(listChats, 0);
                })
                .on('user-data', (userData) => {
                    this.props.dispatch({
                        type: 'CADASTRO_SET_DATA',
                        data: userData
                    });

                })
                .on('mensagem', (listChats) => {
                    //this.dispatchData(listChats, 0);
                    console.log('nova mensagem');
                });
    }

    dispatchData(listChats, idLastMessage) {
        if (listChats) {
            _.forEach(listChats, (listChat) => {
                if (listChat.meusDados) {
                    this.props.dispatch({
                        type: 'CADASTRO_SET_DATA',
                        data: listChat.meusDados
                    });
                }
            });

            this.props.dispatch({
                type: 'LIST_CHAT_LOAD',
                listChats
            });
        }
        if (idLastMessage) {
            this.props.dispatch({
                type: 'CHAT_LAST_ID_MESSAGE',
                idLastMessage
            });
        }
    }
    render() {
        return '';
    }
}

export default connect((state) => {
    const {idLastMessage} = state.currentChat;
    return {
        idLastMessage
    };
})(MsgLoader);
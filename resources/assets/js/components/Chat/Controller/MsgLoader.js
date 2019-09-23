import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';
class MsgLoader extends Component {

    constructor(props) {
        super(props);
        this.loadConversations(true);
        /**
         * Conta o número de carregamentos do chat sem dados
         *  Se a quantidade passar de 10, então aumenta o tempo do interval
         */
        this.numLoadsEmpty = 0;
        this.intervalMaxEmptyCount = 5;
        this.intervalMax = 5000;
        this.intervalIncrement = 1000;
        this.intervalInitial = 1000;
        this.intervalCurrent = this.intervalInitial;
    }

    getUrl(type) {
        return '/' + (this.props[type] || '').replace(/^\/+/, '');
    }
    dispatchData(listChats, idLastMessage) {
        if (listChats) {
            this.props.dispatch({
                type: 'CADASTRO_SET_DATA',
                data: Object.values(listChats)[0]
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
    calculeInterval(listChats) {
        if (listChats && listChats.length === 0) {
            this.numLoadsEmpty++;
            if (this.numLoadsEmpty >= this.intervalMaxEmptyCount) {
                this.numLoadsEmpty = 0;
                if (this.intervalCurrent < this.intervalMax) {
                    this.intervalCurrent += this.intervalIncrement;
                }
            }
        } else {
            this.numLoadsEmpty = 0;
            this.intervalCurrent = this.intervalInitial;
        }
    }
    loadConversations(loop) {
        let url = this.getUrl('urlMensagens');
        var startTime = new Date();
        var params = {};

        if (this.props.idLastMessage) {
            params['idLastMessage'] = this.props.idLastMessage;
        }
        $.getJSON(url, params, (data) => {
            this.lastTimeLoad = new Date() - startTime;
            const {listChats, idLastMessage} = data;
            this.dispatchData(listChats, idLastMessage);
            this.calculeInterval();

        }).always(() => {
            if (loop) {
                setTimeout(() => {
                    this.loadConversations(true);
                }, this.intervalCurrent);
            }
        });
    }
    render() {
        return '';
    }
}
MsgLoader.defaultProps = {
    urlMensagens: '/chat/mensagens'
};
export default connect((state) => {
    const {idLastMessage} = state.currentChat;
    return {
        idLastMessage
    };
})(MsgLoader);
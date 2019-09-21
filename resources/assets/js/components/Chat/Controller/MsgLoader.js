import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';
class MsgLoader extends Component {

    constructor(props) {
        super(props);
        this.loadConversations(true);
    }

    getUrl(type) {
        return '/' + (this.props[type] || '').replace(/^\/+/, '');
    }
    loadConversations(loop) {
        let url = this.getUrl('urlMensagens');
        var startTime = new Date();
        var params = {};
        
        
        
        if(this.props.idLastMessage){
            params['idLastMessage'] = this.props.idLastMessage;
        }
        $.getJSON(url, params, (data) => {
            this.lastTimeLoad = new Date() - startTime;
            const {listChats, idLastMessage} = data;
            this.props.dispatch({
                type: 'CADASTRO_SET_DATA',
                data: Object.values(listChats)[0]
            });
            this.props.dispatch({
                type: 'LIST_CHAT_LOAD',
                listChats
            });
            this.props.dispatch({
                type: 'CHAT_LAST_ID_MESSAGE',
                idLastMessage
            });
            if (loop) {
                setTimeout(() => {
                    this.loadConversations(true);
                }, 1000);
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
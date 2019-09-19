import React, {Component, PropTypes} from 'react';
import _ from 'lodash';
import { connect } from 'react-redux';
import Message from './Message';
class Conversation extends Component {
    constructor() {
        super();
        this.ul = React.createRef();
    }

    componentDidUpdate() {
        var ul = this.ul.current;
        if (ul) {
            ul.scrollTop = ul.scrollHeight;
        }
    }
    render() {
        const {conversation, mensagens} = this.props;
        return (
                <ul className="conversation" ref={this.ul}>
                    {renderMsgs(mensagens, conversation)}
                </ul>
                );
    }
}
function renderMsgs(mensagens, conversation) {
    if (!mensagens || mensagens.length === 0) {
        return ([
            <li className="empty" key="1">Nenhuma mensagem</li>,
            <li className="start-chat" key="2">Digite uma mensagem abaixo para iniciar uma conversa</li>
        ]);
    }
    return _.map(mensagens, (msg, id) => {
        return <Message
        key={id + '-' + msg.idConversa}
        data={msg}
        conversation={conversation} />;
    }).reverse();
}

export default connect((state) => {
    const conversationActive = state.currentChat.conversationActive;
    if (!conversationActive) {
        return {};
    }

    const chatData = state.listChats[conversationActive];

    return {
        conversation: chatData,
        mensagens: [...chatData.mensagens],
    };
})(Conversation);
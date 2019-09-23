import React, {Component, PropTypes} from 'react';
import _ from 'lodash';
import { connect } from 'react-redux';
import Message from './Message';
class Conversation extends Component {
    constructor() {
        super();
        this.ul = React.createRef();

        // Rola o elemento scroll para baixo quando tem nova msg
        this.enableAutoScroll = 1;
        // Detecta se o auto scroll é pelo usuário ou automático
        this.scrollIsAuto = true;
    }
    onScroll(e) {
        // Se o usuário rolar as msgs, desabilita o auto scroll
        if (!this.scrollIsAuto) {
            this.enableAutoScroll = false;
        }

        // Pula a renderização inicial
        if (this.enableAutoScroll === 1) {
            this.enableAutoScroll = true;
            return;
        }
        var ul = this.ul.current;
        if (!ul) {
            return;
        }
        if ((ul.scrollTop >> 0) === (ul.scrollHeight - ul.offsetHeight) >> 0) {
            this.enableAutoScroll = true;
        }
    }
    componentDidUpdate() {
        var ul = this.ul.current;
        if (this.enableAutoScroll && ul) {
            this.scrollIsAuto = true;
            ul.scrollTop = ul.scrollHeight;
            setTimeout(() => {
                this.scrollIsAuto = false;
            });
        }
    }
    render() {
        const {conversation, mensagens} = this.props;
        return (
                <ul className="conversation" ref={this.ul} onScroll={this.onScroll.bind(this)}>
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
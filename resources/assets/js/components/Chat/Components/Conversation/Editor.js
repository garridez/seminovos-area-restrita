import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';

import {createNewMessage} from '../../utils/messages';

class Editor extends Component {
    constructor(props) {
        super(props);
        this.input = React.createRef();
    }

    componentDidUpdate() {
        this.input.current.focus();
    }
    handleSubmit(event) {
        event.preventDefault();
        var msg = this.input.current.value;
        if (!msg || !msg.trim()) {
            return false;
        }
        console.log(this.props.lastIdMessage);
        const message = createNewMessage(
                this.props.conversationActive,
                this.props.idCadastro,
                msg.trim(),
                (this.props.lastIdMessage + 1) + '-');

        this.props.dispatch({
            type: 'CHAT_SEND_MESSAGE',
            message
        });

        this.input.current.value = '';
        this.input.current.focus();

        return false;
    }

    render() {
        var attrDisable = this.props.conversationActive === null;

        return (
                <div className="editor">
                    <form onSubmit={this.handleSubmit.bind(this)} className='d-flex flex-row'>
                        <input
                            type="text"
                            autoFocus={true}
                            name="msg"
                            placeholder="Digite uma mensagem"
                            className="form-control"
                            autoComplete="off"
                            ref={this.input}
                            disabled={attrDisable}
                            onPasteCapture={(event) => event.preventDefault()}
                            onDropCapture={(event) => event.preventDefault()}
                            title="Digite uma mensagem" />
                        <button type="submit" title="Enivar mensagem">
                            <i className="fa fa-paper-plane" aria-hidden="true"></i>
                        </button>
                    </form>
                </div>
                );
    }
}
export default connect(state => {
    const conversationActive = state.currentChat.conversationActive;
    var lastIdMessage = 0;
    if (conversationActive) {
        const msgs = state.listMensagens[conversationActive];
        lastIdMessage = parseInt((Object.keys(msgs).slice(-1)[0] || 0), 10);
    }
    return {
        conversationActive,
        idCadastro: state.cadastro.idCadastro,
        lastIdMessage
    };
})(Editor);
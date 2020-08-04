import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';

import {createNewMessage} from '../../utils/messages';
import  veiculoUtil from '../../utils/veiculo';

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
        var title = 'Digite uma mensagem';
        if (this.props.isAtivoVeiculo === false) {
            attrDisable = true;
            title = 'Conversa desabilitada. Este anúncio está inativo.'
        } else if(this.props.isAtivoVeiculo === null){
            title = '';
        }

        return (
                <div className="editor">
                    <form onSubmit={this.handleSubmit.bind(this)} className='d-flex flex-row'>
                        <input
                            type="text"
                            autoFocus={true}
                            name="msg"
                            placeholder={title}
                            className="form-control"
                            autoComplete="off"
                            ref={this.input}
                            disabled={attrDisable}
                            onPasteCapture={(event) => event.preventDefault()}
                            onDropCapture={(event) => event.preventDefault()}
                            title={title} />
                        <button type="submit" title="Enivar mensagem" disabled={attrDisable}>
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
    var isAtivoVeiculo = null;
    if (conversationActive) {
        const msgs = state.listMensagens[conversationActive];
        var chat = state.listChats[conversationActive];
        if(chat !== null){
            isAtivoVeiculo = veiculoUtil.isAtivo(chat.idStatus);
        }
        lastIdMessage = parseInt((Object.keys(msgs).slice(-1)[0] || 0), 10);
    }
    return {
        conversationActive,
        idCadastro: state.cadastro.idCadastro,
        lastIdMessage,
        isAtivoVeiculo
    };
})(Editor);
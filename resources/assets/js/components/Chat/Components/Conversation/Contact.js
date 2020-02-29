import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';
import { filterUser, isOnline } from '../../utils/user';

class Contact extends Component {

    constructor(props) {
        super(props);
    }

    closeConversation() {
        this.props.dispatch({
            type: 'CHAT_ACTIVE',
            data: {
                idConversa: null
            }
        });
    }

    render() {
        var {data, meusDados} = this.props;
        if (!data) {
            return '';
        }

        var outroContato = filterUser(meusDados.idCadastro, data).responsavelNome;

        var anuncioUrl = 'https://seminovos.com.br/' + data.idVeiculo;

        return (
                <div className="row">
                    <div className="contact col-8">
                        <span className="h4">{outroContato}</span>
                    </div>
                    <div className="contact col-3">
                        <a href={anuncioUrl} target="_BLANK">Ver anúncio</a>
                    </div>
                    <button
                        type="button"
                        title="Fechar conversa"
                        onClick={this.closeConversation.bind(this)}
                        className="col-1 close-conversation">
                        <i className="fa fa-times" aria-hidden="true"></i>
                    </button>
                </div>
                );
    }
}

export default connect((state) => {

    const conversationActive = state.currentChat.conversationActive;
    const data = state.listChats[conversationActive];
    return {
        data: data,
        meusDados: state.cadastro
    };
})(Contact);
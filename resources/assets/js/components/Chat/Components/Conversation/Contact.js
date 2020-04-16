import moment from 'moment';
import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';

import { filterUser, isOnline } from '../../utils/user';

import Confirms from '../../../Confirms';

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

    deleteConversation() {
        //console.log('Deletar')

        var confirm = Confirms.error({
            text: 'A conversa será apagada definitivamente',
            title: 'Deseja mesmo apagar essa conversa?',
            confirmText: 'Sim, apagar',
            negateText: 'Não apagar',
            successText: "Sucesso",
            confirmCallback: () => {
                console.log('Confirm!');
                console.log(this);
                const {idConversa} = this.props.data;
                this.props.dispatch({
                    type: 'CHAT_DELETE_CONVERSA',
                    data: {
                        idConversa,
                        modal: confirm,
                    }
                });
            }
        });
    }

    render() {
        var {data, meusDados} = this.props;
        if (!data) {
            return '';
        }

        var outroContato = filterUser(meusDados.idCadastro, data).responsavelNome;
        var outroContatoMobile = outroContato.split(" ");
        outroContatoMobile = outroContatoMobile[0] + ' ' + outroContatoMobile[1] || '';

        var anuncioUrl = 'https://seminovos.com.br/' + data.idVeiculo;
        var anuncioTitle = `${data.marca} ${data.modelo} ${data.caracteristica}`;
        var anuncioTitleMobile = `${data.marca} ${data.modelo}`;


        var ultimaVezVistoStr = isOnline(data) ? 'online' : 'visto ' + moment(data.ultimaVezVisto).fromNow();

        return (
                <div className="contact-row d-flex justify-content-between">
                    <div className="contact d-flex justify-content-between">
                        <div className="anuncio-data d-flex">
                            <a href={anuncioUrl} target="_BLANK" className="mr-2" title="Ver o anúncio">
                                <img src={data.foto} alt=""/>
                            </a>
                            <div className="title d-flex flex-wrap">
                                <div className="contact-name h4 d-none d-md-flex align-items-center">{outroContato}</div>
                                <div className="contact-name h4 d-flex d-md-none align-items-center">{outroContatoMobile}</div>
                                <div className="anuncio-title d-none d-md-flex align-items-center"><span>{anuncioTitle}</span></div>
                                <div className="anuncio-title d-block d-md-none align-items-center justify-content-between">
                                    <div>
                                        <span>{anuncioTitleMobile}</span>
                                    </div>
                                    <div className="last-seen align-self-end mr-2">
                                        {ultimaVezVistoStr}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="last-seen d-none d-mb-block align-self-end mr-2">
                            {ultimaVezVistoStr}
                        </div>
                    </div>
                    <button
                        type="button"
                        title="Fechar conversa"
                        onClick={this.closeConversation.bind(this)}
                        className="close-conversation">
                        <i className="fa fa-times" aria-hidden="true"></i>
                    </button>
                
                
                
                
                    <div className="conversation-options-dropdown">
                        <a
                            title="Opções"
                            className="conversation-options dropdown--toggle"
                            data-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false">
                            <i className="fa fa-ellipsis-v" aria-hidden="true"></i>
                        </a>
                        <div className="dropdown-menu">
                            <a
                                className="dropdown-item"
                                href="#"
                                onClick={this.deleteConversation.bind(this)}>Excluir conversa</a>
                            <a
                                className="dropdown-item"
                                href="#"
                                onClick={this.closeConversation.bind(this)}>Fechar</a>
                        </div>
                    </div>
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
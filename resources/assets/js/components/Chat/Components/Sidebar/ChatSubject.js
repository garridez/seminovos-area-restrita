import React, {Component} from 'react';
import { connect } from 'react-redux';
import moment from 'moment';
import _ from 'lodash';
import 'moment/locale/pt-br';

import { isSendedForMe } from '../../utils/messages';
import  veiculoUtil from '../../utils/veiculo';
import { filterUser, isOnline } from '../../utils/user';

class ChatSubject extends Component {
    constructor() {
        super();
        this.active = this.active.bind(this);
    }
    active() {
        if(this.props.data.lastMessage.lidoEm === null) {
            this.props.data.lastMessage.lidoEm = moment().format('YYYY-MM-DD HH:mm:ss');
        }

        this.props.dispatch({
            type: 'CHAT_ACTIVE',
            data: this.props.data
        });

    }
    formatDate(enviadoEm) {
        var chatDate = moment(enviadoEm);
        var now = moment().diff(chatDate, 'days');
        var absoluteDate = chatDate.format('LLLL');
        if (now <= 1) {
            chatDate = chatDate.format('LT');
        } else if (now <= 5) {
            chatDate = chatDate.format('dddd');
        } else {
            chatDate = chatDate.format('l');
        }
        return {
            chatDate,
            absoluteDate
        };
    }
    render() {
        const {data, isActive, meusDados} = this.props;

        var veiculoStatus = '';
        if(!veiculoUtil.isAtivo(data.idStatus)){
            var veiculoStatus = <span className="badge">Anúncio inativo</span>;
        }

        var lastMsg = data.lastMessage;
        var dates = this.formatDate(lastMsg.enviadoEm);
        var status = isOnline(data) ? 'user-status online' : 'user-status offline';
        var outroContato = filterUser(meusDados.idCadastro, data).responsavelNome;

        var classes = 'chat-subject chat row py-2 px-2';
        if (isActive) {
            classes += ' active';
        }

        if (lastMsg.lidoEm === null && !isSendedForMe(meusDados, lastMsg)) {
            classes += ' nao-lida';
        }
        return (
                <li className={classes} onClick={this.active}>
                    <div className="chat-img col-3">
                        <div className="chat-title">
                            <span>{data.marca} {data.modelo}</span>
                        </div>
                        <img src={data.foto} alt="" className="img-fluid"/>
                    </div>
                    <div className="chat-details col-9">
                        <div className="chat-name">
                            <span className={status}></span> {outroContato}:
                        </div>
                        <div className="chat-last-msg">
                            <i className="fa fa-circle fa-icon-nao-lida" title="Você tem mensagens não lidas"></i>
                            {lastMsg.mensagem}
                        </div>
                        {veiculoStatus}
                    </div>
                    <div className="chat-info">
                        <div className="chat-status px-2"></div>
                        
                        <div className="chat-date" title={dates.absoluteDate}>
                            {dates.chatDate}
                        </div>
                    </div>
                </li>
                );
    }
}


export default connect((state, ownProps) => {

    return {
        data: {...state.listChats[ownProps.idConversa]},
        meusDados: state.cadastro
    };
})(ChatSubject);
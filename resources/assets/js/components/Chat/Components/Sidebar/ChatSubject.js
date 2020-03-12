import React, {Component} from 'react';
import { connect } from 'react-redux';
import moment from 'moment';
import _ from 'lodash';
import 'moment/locale/pt-br';

import { isSendedForMe } from '../../utils/messages';
import { filterUser, isOnline } from '../../utils/user';

class ChatSubject extends Component {
    constructor() {
        super();
        this.active = this.active.bind(this);
    }
    active() {
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
        } else if (now <= 2) {
            chatDate = chatDate.calendar();
        } else {
            chatDate = chatDate.format('dddd');
        }
        return {
            chatDate,
            absoluteDate
        };
    }
    render() {
        const {data, isActive, meusDados} = this.props;

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
                        <img src={data.foto} alt="" className="img-fluid"/>
                    </div>
                    <div className="chat-details col-9">
                        <div className="chat-name mt-1">
                            <span className={status}></span> {outroContato}
                        </div>
                        <div className="chat-title">
                            <span>{data.marca} {data.modelo}</span>
                        </div>
                    </div>
                    <div className="chat-info">
                        <div className="chat-date d-flex justify-content-center" title={dates.absoluteDate}>
                            {dates.chatDate}
                        </div>
                        <div className="chat-status px-2"></div>
                        <i className="fa fa-circle icon-nao-lida" title="Você tem mensagnes não lidas"></i>
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
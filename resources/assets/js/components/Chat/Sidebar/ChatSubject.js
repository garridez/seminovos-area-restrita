import React, {Component} from 'react';
import { connect } from 'react-redux';
import moment from 'moment';
import _ from 'lodash';
import 'moment/locale/pt-br';

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
    render() {
        const {data, isActive} = this.props;
        var lastMsg = Object.values(data.mensagens)[0];

        var classes = 'chat-subject chat row py-2 px-2';
        if (isActive) {
            classes += ' active';
        }
        var chatDate = moment(lastMsg.enviadoEm);
        var now = moment().diff(chatDate, 'days');
        var absoluteDate = chatDate.format('LLLL');
        if (now <= 1) {
            chatDate = chatDate.format('LT');
        } else if (now <= 2) {
            chatDate = chatDate.calendar();
        } else {
            chatDate = chatDate.format('dddd');
        }

        return (
                <li className={classes} onClick={this.active}>
                    <div className="chat-img col-4">
                        <img src={data.foto} alt="" className="img-fluid"/>
                    </div>
                    <div className="chat-details col-8">
                        <div className="chat-title">
                            <b>{data.marca} {data.modelo}</b>
                        </div>
                        <div className="chat-name mt-1">
                            {data.responsavelNomeInteressado}
                        </div>
                        <div className="chat-last-msg mt-1" title={lastMsg.mensagem}>
                            {lastMsg.mensagem}
                        </div>
                    </div>
                    <div className="chat-info">
                        <div className="chat-date d-flex justify-content-center" title={absoluteDate}>
                            {chatDate}
                        </div>
                        <div className="chat-status px-2"></div>
                    </div>
                </li>
                );
    }
}


export default connect((state, ownProps) => {

    return {
        data: {...state.listChats[ownProps.idConversa]}
    };
})(ChatSubject);
import {Component} from 'react';
import moment from 'moment';
import _ from 'lodash';
import 'moment/locale/pt-br';

export default class ChatSubject extends Component {
    constructor() {
        super()
        this.active = this.active.bind(this);
    }
    active() {
        var idConversa = this.props.data.idConversa;
        this.props.onActive(idConversa);
    }
    render() {
        const {data, isActive} = this.props
        var lastMsg = Object.values(data.mensagens)[0];

        var classes = 'chat-subject chat bg-white d-flex py-2 px-2 border-bottom';
        if (isActive) {
            classes += ' active';
        }

        return (
                <div className={classes} onClick={this.active}>
                    <div className="chat-img mr-2">
                
                        <img src={data.foto} alt="" className="img-fluid"/>
                    </div>
                    <div className="chat-details">
                        <div className="chat-title">
                            <b>{data.marca} {data.modelo}</b>
                        </div>
                        <div className="chat-name mt-1">
                            {data.responsavelNomeInteressado}
                        </div>
                        <div className="chat-last-msg mt-1 text-gray">
                            {lastMsg.mensagem}
                        </div>
                    </div>
                    <div className="chat-info ml-auto">
                        <div className="chat-date d-flex justify-content-center">
                            {moment(lastMsg.enviadoEm).calendar()}
                        </div>
                        <div className="chat-status px-2">
                            status
                        </div>
                    </div>
                </div>
                );
    }
}


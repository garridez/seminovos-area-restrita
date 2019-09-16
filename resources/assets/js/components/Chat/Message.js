import {Component} from 'react';
import moment from 'moment';

export default class Message extends Component {
    constructor() {
        super()
        this.state = {}
    }
    render() {
        const {conversation, data} = this.props;

        var deQuem = 'message ';
        deQuem += conversation.meuIdCadastro === data.idCadastroRemetente ? 'send-from-me' : 'send-from-interested';

        return (
                <li className={deQuem}>
                    <div className="msg">{data.mensagem}</div>
                    <div className="info">
                        <div className="time">
                            {moment(data.enviadoEm).format('h:mm')}
                        </div>
                    </div>
                </li>
                );
    }
}
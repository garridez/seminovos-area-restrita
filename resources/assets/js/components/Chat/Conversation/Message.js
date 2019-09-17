import React, {Component} from 'react';
import moment from 'moment';

export default class Message extends Component {
    constructor() {
        super()
        this.state = {}
    }
    render() {
        const {conversation, data} = this.props;

        var liClass = [
            'message',
            conversation.meuIdCadastro === data.idCadastroRemetente ? 'sent' : 'received',
        ];
        var enviadoEm = moment(data.enviadoEm);
        return (
                <li className={liClass.join(' ')}>
                    <div className="text">{data.mensagem}</div>
                    <div className="time" title={enviadoEm.format('LLLL')}>
                        {enviadoEm.format('h:mm')}
                    </div>
                </li>
                );
    }
}
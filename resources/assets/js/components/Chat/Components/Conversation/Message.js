import React, {Component} from 'react';
import moment from 'moment';

export default class Message extends Component {
    constructor() {
        super()
        this.state = {}
    }
    render() {
        const {conversation, data, meusDados} = this.props;
        var liClass = [
            'message',
            meusDados.idCadastro !== data.idCadastroRemetente ? 'received' : 'sent',
        ];
        var enviadoEm = moment(data.enviadoEm);

        if (data.delivered !== undefined) {
            liClass.push(data.delivered ? 'delivered' : 'not-delivered');
        }

        return (
                <li className={liClass.join(' ')}>
                    <div className="text">{data.mensagem} - {data.idChatMensagem}</div>
                    <div className="time" title={enviadoEm.format('LLLL')}>
                        {enviadoEm.format('h:mm')}
                    </div>
                </li>
                );
    }
}
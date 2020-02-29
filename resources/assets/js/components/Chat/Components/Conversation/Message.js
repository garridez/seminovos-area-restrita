import React, {Component} from 'react';
import moment from 'moment';
import {isSendedForMe} from '../../utils/messages';

export default class Message extends Component {
    constructor() {
        super()
        this.state = {}
    }
    render() {
        const {conversation, data, meusDados} = this.props;
        const isSendedForMeBool = isSendedForMe(meusDados, data);
        const enviadoEm = moment(data.enviadoEm);

        var liClass = [
            'message',
            isSendedForMeBool ? 'sent' : 'received',
        ];

        var statusTitle = '';
        var statusIcon = '';

        if (data.delivered !== undefined) {
            liClass.push(data.delivered ? 'delivered' : 'not-delivered');
            statusTitle = data.delivered ? 'Entregue' : 'Enviando...';
            statusIcon = data.delivered ? 'check' : 'clock-o';
        }

        if (isSendedForMeBool) {
            statusIcon = statusIcon || 'check';
            if (data.lidoEm) {
                liClass.push('lida');
                statusTitle = 'Lida';
            }
        }


        statusIcon = 'fa fa-' + statusIcon;

        return (
                <li className={liClass.join(' ')}>
                    <div className="text">{data.mensagem}</div>
                    <div className="infos">
                        <div className="time" title={enviadoEm.format('LLLL')}>
                            {enviadoEm.format('LT')} &nbsp;
                        </div>
                        <div className="status" title={statusTitle}>
                            <i className={statusIcon} aria-hidden="true"></i>
                        </div>
                    </div>
                </li>
                );
    }
}
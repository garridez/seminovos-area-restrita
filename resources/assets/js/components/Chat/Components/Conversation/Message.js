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

        var messageDelivery = isSendedForMeBool ? 'sent' : 'received';
        var messageStatusClass = [
            'message-container',
            isSendedForMeBool ? 'sent' : 'received'
        ];

        var statusTitle = '';
        var statusIcon = '';
        var lidaIcon = '';

        if (data.delivered !== undefined) {
            messageStatusClass.push(data.delivered ? 'delivered' : 'not-delivered');
            statusTitle = data.delivered ? 'Entregue' : 'Enviando...';
            statusIcon = data.delivered ? 'check' : 'clock-o';
        }

        if (isSendedForMeBool) {
            statusIcon = statusIcon || 'check';
            if (data.lidoEm) {
                messageStatusClass.push('lida');
                statusTitle = 'Lida';
                lidaIcon = <i className="fa fa-check" aria-hidden="true"></i>
            }
        }


        statusIcon = 'fa fa-' + statusIcon;

        return (
                <li className={"message " + messageDelivery}>
                    <div className={messageStatusClass.join(' ')}>
                        <div className="text">{data.mensagem}</div>
                        <div className="infos">
                            <div className="time" title={enviadoEm.format('LLLL')}>
                                {enviadoEm.format('L LT')} &nbsp;
                            </div>
                            <div className="status" title={statusTitle}>
                                <i className={statusIcon} aria-hidden="true"></i>
                                {lidaIcon}
                            </div>
                        </div>
                    </div>
                </li>
                );
    }
}
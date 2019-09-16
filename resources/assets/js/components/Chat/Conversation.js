import React, {Component, PropTypes} from 'react';
import _ from 'lodash';
import Message from './Message';

export default class Conversation extends Component {
    constructor() {
        super()
    }
    render() {
        const {conversation, mensagens} = this.props;
        return (
                <ul className="conversation">
                    {
                        _.map(mensagens, (msg, id) => <Message key={id} data={msg} conversation={conversation}/>)
                    }
                </ul>
                );
    }
}

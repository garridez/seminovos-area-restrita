import React, {Component, PropTypes} from 'react';
import ReactDOM from 'react-dom';
import _ from 'lodash';
import ChatSubject from './ChatSubject';

export default class ListChats extends Component {
    constructor() {
        super()
    }

    render() {
        const {listChats} = this.props
        const {onActive} = this.props;
        const {conversationActive} = this.props;
        var params = {
            listChats: listChats || {},
            onActive,
            conversationActive
        };

        return (
                <ul className="list-chats">
                    {renderListChats(params)}
                </ul>
                );
    }
}

function renderListChats(params) {
    var {listChats, onActive, conversationActive} = params;

    if (conversationActive) {
//        console.log(conversationActive);
//        console.log(listChats);
    }
    listChats = _.sortBy(listChats, function (v) {
        var {mensagens} = v;
        var lastKey = _.findLastKey(mensagens);
        return mensagens[lastKey].enviadoEm;
    }).reverse();

    return  _.map(listChats, function (v, k) {
        return <ChatSubject
            key={v.idConversa}
            data={v}
            isActive={conversationActive === v.idConversa}
            onActive={onActive}/>;
    });
}

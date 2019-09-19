import React, {Component} from 'react';
import { connect } from 'react-redux';
import _ from 'lodash';

import ChatSubject from './ChatSubject';

class ListChats extends Component {
    constructor() {
        super()
    }

    render() {
        const {listChats} = this.props;
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

    listChats = _.sortBy(listChats, function (v) {
        return v.mensagens[0].enviadoEm;
    }).reverse();

    return  _.map(listChats, function (chatData, k) {
        return <ChatSubject
            key={chatData.idConversa}
            idConversa={chatData.idConversa}
        
            isActive={conversationActive === chatData.idConversa}
            onActive={onActive}/>;
    });
}




export default connect((state) => {

    const listChats = {...state.listChats};
    const conversationActive = state.currentChat.conversationActive;

    return {
        listChats,
        conversationActive
    };
})(ListChats);
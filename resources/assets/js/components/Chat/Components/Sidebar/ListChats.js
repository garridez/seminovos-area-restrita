import React, {Component} from 'react';
import { connect } from 'react-redux';
import _ from 'lodash';

import ChatSubject from './ChatSubject';

class ListChats extends Component {
    constructor() {
        super();
    }

    render() {
        const {listChats} = this.props;
        const {conversationActive} = this.props;
        const {filter} = this.props;
        var params = {
            listChats: listChats || {},
            conversationActive,
            filter
        };

        return (
                <ul className="list-chats">
                    {renderListChats(params)}
                </ul>
                );
    }
}

function renderListChats(params) {
    var {listChats, onActive, conversationActive, filter} = params;

    listChats = _.sortBy(listChats, function (v) {
        return v.lastMessage.enviadoEm;
    }).reverse();

    if (filter && filter.text) {
        var text = filter.text.toLowerCase();
        listChats = listChats.filter((e) => {
            if (e.responsavelNomeInteressado.toLowerCase().indexOf(text) !== -1) {
                return true;
            }
            if (e.marca.toLowerCase().indexOf(text) !== -1) {
                return true;
            }
            if (e.modelo.toLowerCase().indexOf(text) !== -1) {
                return true;
            }
            if (e.caracteristica.toLowerCase().indexOf(text) !== -1) {
                return true;
            }
            return false;
        });
    }

    return  _.map(listChats, function (chatData, k) {
        return <ChatSubject
            key={chatData.idConversa}
            idConversa={chatData.idConversa}
        
            isActive={conversationActive === chatData.idConversa}
            />;
    });
}




export default connect((state) => {

    const listChats = {...state.listChats};
    const conversationActive = state.currentChat.conversationActive;
    const filter = state.filter;

    return {
        listChats,
        conversationActive,
        filter
    };
})(ListChats);
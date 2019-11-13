import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';

class Contact extends Component {

    constructor(props) {
        super(props);
    }

    closeConversation() {
        this.props.dispatch({
            type: 'CHAT_ACTIVE',
            data: {
                idConversa: null
            }
        });
    }

    render() {
        var {data} = this.props;
        if (!data) {
            return '';
        }
        data = {...data};

        return (
                <div className="row">
                    <div className="contact col-11">
                        <span className="h2">{data.responsavelNomeInteressado}</span>
                    </div>
                    <button
                        type="button"
                        title="Fechar conversa"
                        onClick={this.closeConversation.bind(this)}
                        className="col-1 close-conversation">
                        <i className="fa fa-times" aria-hidden="true"></i>
                    </button>
                </div>
                );
    }
}

export default connect((state) => {

    const conversationActive = state.currentChat.conversationActive;
    const data = state.listChats[conversationActive];
    return {
        data: data
    };
})(Contact);
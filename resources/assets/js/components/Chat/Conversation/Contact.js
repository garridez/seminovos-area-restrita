import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';

class Contact extends Component {

    constructor(props) {
        super(props);
    }

    render() {
        var {data} = this.props;
        if (!data) {
            return '';
        }
        data = {...data};

        return (
                <div className="contact">
                    <span className="h2">{data.responsavelNomeInteressado}</span>
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
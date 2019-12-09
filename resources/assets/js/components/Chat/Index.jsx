import React, { Component, PropTypes } from 'react';
import ReactDOM from 'react-dom';
import { connect } from 'react-redux';
import _ from 'lodash';

import Loading from './Containers/Loading';
import ChatContainer from './Containers/Chat';
import NoMsgs from './Containers/NoMsgs';

class Chat extends Component {

    render() {
        const { numChats } = this.props;

        if (numChats > 0) {
            return <ChatContainer />;
        }

        if (numChats === false) {
            return <Loading />;
        }

        return <NoMsgs />;
    }
}

export default connect((state, ownProps) => {
    var numChats = false;
    if (state.listChats) {
        numChats = _.size(state.listChats);
    }
    return {
        numChats
    };
})(Chat);
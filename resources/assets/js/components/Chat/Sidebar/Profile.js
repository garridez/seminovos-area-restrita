import React, {Component} from 'react';
import _ from 'lodash';


export default class Profile extends Component {
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
                <div className="profile">Profile</div>
                );
    }
}
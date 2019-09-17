import React, {Component} from 'react';
import _ from 'lodash';


export default class Filter extends Component {
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
                <div class="filter">
                    <input type="text" placeholder="Procurar uma conversa" className="form-control"/>
                </div>
                );
    }
}
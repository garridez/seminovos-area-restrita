import React, {Component} from 'react';
import { connect } from 'react-redux';
import _ from 'lodash';


class Filter extends Component {
    constructor() {
        super();
        this.input = React.createRef();
    }

    handleFilter() {
        this.props.dispatch({
            type: 'LIST_CHAT_FILTER',
            text: this.input.current.value
        });
    }
    render() {

        return (
                <div className="filter">
                    <input type="text"
                           placeholder="Procurar uma conversa"
                           className="form-control"
                           onKeyUp={this.handleFilter.bind(this)}
                           ref={this.input}
                           />
                </div>
                );
    }
}

export default connect()(Filter);
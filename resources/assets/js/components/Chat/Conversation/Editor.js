
import React, { Component, PropTypes } from 'react';

export default class Editor extends Component {

    constructor(props) {
        super(props)
    }

    render() {
        return (
                <div className="editor">
                    <input type="text" placeholder="Digite uma mensagem" className="form-control"/>
                </div>
                );
    }
}

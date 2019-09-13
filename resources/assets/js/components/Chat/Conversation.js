import React, {Component, PropTypes} from 'react';
import ReactDOM from 'react-dom';
window.React = React;
window.ReactDOM = ReactDOM;
//var chat = <section className="section-chat">
export default class Conversation extends Component {
    constructor() {
        super()
        this.state = {filter: ''}
    }

//    handleFilterUpdate = (filter) => {;;
//        this.setState({filter})
//    }

    render() {
//        const {username} = this.props.params
//        const {filter} = this.state
        // <Message/>
        return (
                <ul>
                </ul>
                );
    }
}



//export default chat;

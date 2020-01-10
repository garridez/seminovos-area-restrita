import React, { Component } from 'react';

class Loading extends Component {

    render() {
        return (
                <div className="loading-container">
                    <div className="text-center">
                        <div className="spinner-border text-laranja" role="status">
                            <span className="sr-only">Loading...</span>
                        </div>
                        <div className="feedback-text text-white animated pulse infinite">
                            Carregando...
                        </div>
                    </div>
                </div>
                );
    }
}

export default Loading;
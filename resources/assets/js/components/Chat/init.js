
import React from 'react';
import ReactDOM from 'react-dom';

import Chat from 'components/Chat';

export default function () {
    return ReactDOM.render(
            <Chat baseUrl=""/>,
            document.querySelector('.chat-root')
            );
}
 
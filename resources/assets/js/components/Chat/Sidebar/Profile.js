import React, {Component} from 'react';
import { connect } from 'react-redux';
import _ from 'lodash';


class Profile extends Component {
    constructor() {
        super();
    }

    render() {
        var {data} = this.props;
        if (!data) {
            return '';
        }
        data = {...data};

        return (
                <div className="profile">
                <span className="h2">{data.responsavelNome}</span>
                </div>
                );
    }
}

export default connect((state) => {
    const data = state.cadastro || false;
    return {
        data: data
    };
})(Profile);
import _ from 'lodash';

const initialState = {
    idCadastro: null
};
const dataProperties = [
    'idCadastro',
    'responsavelNome'
];
export default (state = initialState, action) => {
    switch (action.type) {
        case 'CADASTRO_SET_DATA':
            var data = {};
            _.map(action.data, (v, k) => {
                if (dataProperties.indexOf(k) === -1) {
                    return;
                }
                data[k] = v;
            });
            return {
                ...state,
                ...data
            };

        default:
            return state;
}
};



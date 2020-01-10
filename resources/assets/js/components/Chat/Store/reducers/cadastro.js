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
            if (!action.data) {
                return state;
            }
            
            return {
                ...state,
                ...action.data
            };

        default:
            return state;
}
};



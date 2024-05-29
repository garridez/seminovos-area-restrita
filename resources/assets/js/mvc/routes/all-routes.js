import Mask from '../../components/Mask';
import SetAjaxLoadding from '../../components/SetAjaxLoadding';
import Livereload from '../../components/Livereload';

export const seletor = 'body';
export const prepend = true; // Esse script precisa rodar primeiro
export const callback = () => {
    Mask();
    SetAjaxLoadding();
    Livereload();
    //require('components/Mask')();
    //require('components/SetAjaxLoadding')();
    //require('components/Livereload')();
};

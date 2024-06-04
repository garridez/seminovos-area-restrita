import $ from 'jquery';
import loading from './Loading';

export default function () {
    if (window.setAjaxLoadding === false) {
        return;
    }

    $(document)
        .ajaxStart(function () {
            if (window.setAjaxLoadding === false) {
                return;
            }
            loading.open();
        })
        .ajaxComplete(function () {
            if (window.setAjaxLoadding === false) {
                return;
            }
            loading.close();
        });
}

import $ from 'jquery';
import Loading from './Loading';

export default function () {
    if (window.setAjaxLoadding === false) {
        return;
    }

    $(document)
        .ajaxStart(function () {
            if (window.setAjaxLoadding === false) {
                return;
            }
            Loading.open();
        })
        .ajaxComplete(function () {
            if (window.setAjaxLoadding === false) {
                return;
            }
            Loading.close();
        });
}

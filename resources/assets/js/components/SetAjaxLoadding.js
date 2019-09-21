
module.exports = function () {
    if (window.setAjaxLoadding === false) {
        return;
    }
    var $ = require('jquery');
    var loading = require('components/Loading');
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
};
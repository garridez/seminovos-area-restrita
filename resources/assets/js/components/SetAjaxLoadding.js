
module.exports = function () {
    var $ = require('jquery');
    var loading = require('components/Loading');
    $(document)
            .ajaxStart(function () {
                loading.open();
            })
            .ajaxComplete(function () {
                loading.close();
            });
};
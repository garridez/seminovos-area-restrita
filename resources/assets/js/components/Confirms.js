
require('components/JsBsModal');

var $ = require('jquery');
var confirms;

module.exports = confirms = {
    options: {},
    alert: function (type, text, title, img) {
        if (title === null || title === undefined) {
            title = '';
        }
        if (img === null || img === undefined) {
            img = '';
        }

        var close = $('<button class="btn btn-danger" data-dismiss="modal">')
            .html('<span class="text-close">Fechar</span> ');



        var modal = $.jsBsModal({
            contents: {
                'close': '',
                'modal-title': title,
                'modal-body': text,
                'modal-footer': [
                    close
                ],
            }
        }).on('hidden.bs.modal', function () {
            modal.modal('dispose').remove();
        });

        modal.find('.modal-content').addClass('alert alert-' + type);

        return modal;
    },
    success: function (text, title) {
        return alerts.alert('success', text, title);
    },
    info: function (text, title) {
        return alerts.alert('info', text, title);
    },
    warning: function (text, title) {
        return alerts.alert('warning', text, title);
    },
    error: function (text, title) {
        return alerts.alert('error', text, title);
    },
};
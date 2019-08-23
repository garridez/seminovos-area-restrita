
require('components/JsBsModal');

var $ = require('jquery');
var alerts;

module.exports = alerts = {
    options: {
        time: 5000
    },
    alert: function (type, text, title, time) {
        if (title === null || title === undefined) {
            title = '';
        }
        time = time || this.options.time;
        var count = Math.floor(time / 1000);
        var spanCount = $('<span class="text-count"></span>').text('(' + count + ')');
        var close = $('<button class="btn btn-primary" data-dismiss="modal">')
            .html('<span class="text-close">Fechar</span> ')
            .append(spanCount);

        var intervalID = setInterval(function () {
            spanCount.text('(' + count + ')');
            count--;
            if (count < 0) {
                clearInterval(intervalID);
            }

        }, 1000);

        setTimeout(function () {
            modal.modal('hide');
        }, time);

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
    success: function (text, title, time) {
        return alerts.alert('success', text, title, time);
    },
    info: function (text, title, time) {
        return alerts.alert('info', text, title, time);
    },
    warning: function (text, title, time) {
        return alerts.alert('warning', text, title, time);
    },
    error: function (text, title, time) {
        return alerts.alert('error', text, title, time);
    },
};
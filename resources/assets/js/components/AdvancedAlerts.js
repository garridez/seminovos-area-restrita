require('components/JsBsModal');

var $ = require('jquery');
var advancedAlerts;

module.exports = advancedAlerts = {
    optionsDefault: {
        text: '',
        title: '',
        time: 5000,
        img: false,
        closeText: 'Fechar',
        closeCallback: function () {
            return;
        },
    },
    alert: function (type, options) {
        options = $.extend({}, this.optionsDefault, options);
        var close = $('<button class="btn btn-primary" data-dismiss="modal">')
            .html(`<span class="text-close">${options.closeText} </span>`)
            .click(function () {});

        if (typeof options.img === 'string' && options.img !== '') {
            options.img = $(`<img src="${options.img}" class="modal-img">`);
        }
        if (options.time) {
            var count = Math.floor(options.time / 1000);
            var spanCount = $('<span class="text-count"></span>').text('(' + count + ')');
            close.append(spanCount);
            var intervalID = setInterval(function () {
                spanCount.text('(' + count + ')');
                count--;
                if (count < 0) {
                    clearInterval(intervalID);
                }
            }, 1000);
            setTimeout(function () {
                modal.modal('hide');
            }, options.time);
        }

        if (options.close == '') {
            close = '';
        }

        var modal = $.jsBsModal({
            contents: {
                close: '',
                'modal-title': [options.img, options.title],
                'modal-body': options.text,
                'modal-footer': [close],
            },
        }).on('hidden.bs.modal', function () {
            options.closeCallback();
            modal.modal('dispose').remove();
        });

        modal.find('.modal-content').addClass('alert alert-' + type);

        return modal;
    },
    success: function (options) {
        return advancedAlerts.alert('success', options);
    },
    info: function (options) {
        return advancedAlerts.alert('info', options);
    },
    warning: function (options) {
        return advancedAlerts.alert('warning', options);
    },
    error: function (options) {
        return advancedAlerts.alert('error', options);
    },
};

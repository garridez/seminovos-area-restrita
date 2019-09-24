
require('components/JsBsModal');

var $ = require('jquery');
var formAlerts;

module.exports = formAlerts = {
    optionsDefault: {
        form: "",
        title: "",
        img: false,
        submitText: "Salvar",
        closeCallback: function () { return },
        submitCallback: function () { return }
    },
    alert: function (type, options) {
        options = $.extend({}, this.optionsDefault, options);
        var submit = $(`<button class="btn bg-verde text-white">`)
                    .html(`<span class="text-submit">${options.submitText}</span>`)
                    .click(function () {options.submitCallback()});
        if (typeof options.img === "string" && options.img !== "") {
            options.img = $(`<img src="${options.img}" class="modal-img">`)
        }
        var modal = $.jsBsModal({
            contents: {
                'close': '',
                'modal-title': [options.img, options.title],
                'modal-body': options.form,
                'modal-footer': [
                    submit
                ],
            }
        }).on('hidden.bs.modal', function () {
            options.closeCallback();
            modal.modal('dispose').remove();
        });
        modal.find('.modal-content').addClass('alert alert-' + type);
        return modal;
    },
    success: function (options) {
        return formAlerts.alert('success', options);
    },
    info: function (options) {
        return formAlerts.alert('info', options);
    },
    warning: function (options) {
        return formAlerts.alert('warning', options);
    },
    error: function (options) {
        return formAlerts.alert('error', options);
    },
};
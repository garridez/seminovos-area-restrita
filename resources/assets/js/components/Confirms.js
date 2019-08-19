
require('components/JsBsModal');

var $ = require('jquery');
var confirms;

module.exports = confirms = {
    optionsDefault: {
        text:"",
        title: "",
        img: "",
        confirmText: "Salvar Alterações",
        negateText: "Fechar",
        confirmCallback:function(){return},
        negateCallback:function(){return}
    },
    confirm: function (type, options) {
        options = $.extend({}, this.optionsDefault, options);
        /**
         * @todo Melhorar estes IFs
         */
        if(options.img !== "") {
            options.img = $(`<img class="modal-img" src="${options.img}">`);
        }

        var btnConfirm = $('<button type="button" class="btn btn-primary">')
            .html(`<span>${options.confirmText}</span>`).click(function () {
                options.confirmCallback();
            });

        var btnNegate = $('<button type="button" class="btn btn-secondary" data-dismiss="modal">')
            .html(`<span>${options.negateText}</span>`).click(function () {
                options.negateCallback();
            });

        var modal = $.jsBsModal({
            contents: {
                'close': false,
                'modal-title': [options.img, options.title],
                'modal-body': options.text,
                'modal-footer': [
                    btnNegate, btnConfirm
                ],
            }
        }).on('hidden.bs.modal', function () {
            modal.modal('dispose').remove();
        });

        modal.find('.modal-content').addClass('confirm confirm-' + type);

        return modal;
    },
    success: function (options) {
        return confirms.confirm('success', options);
    },
    info: function (options) {
        return confirms.confirm('info', options);
    },
    warning: function (options) {
        return confirms.confirm('warning', options);
    },
    error: function (options) {
        return confirms.confirm('error', options);
    },
};

require('components/JsBsModal');

var $ = require('jquery');
var confirms;

module.exports = confirms = {
    options: {
        title: "",
        img: "",
        confirmText: "Salvar Alterações",
        negateText: "Fechar"
    },
    confirm: function (type, text, title, img, confirmText, negateText) {
        /**
         * @todo Melhorar estes IFs
         */
        if (title === null || title === undefined) {
            title = this.options.title;
        }
        console.log(img);

        if (img === null || img === undefined) {
            img = this.options.img;
        } else {
            img = $(`<img class="modal-img" src="${img}">`);
        }
        if (confirmText === null || confirmText === undefined) {
            confirmText = this.options.confirmText;
        }
        if (negateText === null || negateText === undefined) {
            negateText = this.options.negateText;
        }

        var btnConfirm = $('<button type="button" class="btn btn-primary">')
            .html(`<span>${confirmText}</span>`);

        var btnNegate = $('<button type="button" class="btn btn-secondary" data-dismiss="modal">')
            .html(`<span>${negateText}</span>`);

        var modal = $.jsBsModal({
            contents: {
                'close': false,
                'modal-title': [img, title],
                'modal-body': text,
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
    success: function (text, title, img) {
        return confirms.confirm('success', text, title, img);
    },
    info: function (text, title, img) {
        return confirms.confirm('info', text, title, img);
    },
    warning: function (text, title, img) {
        return confirms.confirm('warning', text, title, img);
    },
    error: function (text, title, img) {
        return confirms.confirm('error', text, title, img);
    },
};
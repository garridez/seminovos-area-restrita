
require('components/JsBsModal');
var advancedAlerts = require('components/AdvancedAlerts');

var $ = require('jquery');
var confirms;

module.exports = confirms = {
    optionsDefault: {
        text: "",
        title: "",
        img: "",
        confirmText: "Sim",
        negateText: "Não",
        successText: "Sucesso",
        reloadPage: false,
        confirmCallback: function () { return },
        negateCallback: function () { return }
    },
    confirm: function (type, options) {
        options = $.extend({}, this.optionsDefault, options);
        /**
         * @todo Melhorar estes IFs
         */
        if (options.img !== "") {
            options.img = $(`<img class="modal-img" src="${options.img}">`);
        }

        var btnConfirm = $('<button type="button" class="btn btn-primary">')
            .html(`<span>${options.confirmText}</span>`).click(function () {
                $(this).attr("disabled", true);
                if (typeof options.confirmCallback === 'function') {
                    options.confirmCallback();
                } else if (typeof options.confirmCallback === 'string') {
                    $(".modal").modal('hide');
                    $.getJSON(options.confirmCallback)
                        .done(function (data, jqXHR, type) {
                            if (data.status !== 200) {
                                advancedAlerts.error({
                                    text: data.detail,
                                    title: "Houve um problema...",
                                })
                            } else {
                                advancedAlerts.success({
                                    text: options.successText,
                                    title: $("<span class='text-primary'>").html("Sucesso")
                                });

                                if (options.reloadPage) {
                                    setTimeout(() => {
                                        document.location.reload(true);
                                    }, 3000);                                    
                                }
                            }
                            $(".modal").modal('hide');
                        }).fail(function () {
                            advancedAlerts.error({ title: "ERRO", text: "Não conseguimos processar sua requisição, tente novamente mais tarde" });
                        })
                }
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
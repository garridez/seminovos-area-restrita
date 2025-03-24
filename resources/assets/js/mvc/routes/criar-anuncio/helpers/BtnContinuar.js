import $ from 'jquery';

var methods = {
    get: function () {
        return $('.step-controls .btn-continuar');
    },
    disable: function () {
        return methods
            .get()
            .addClass('disabled')
            .prop('disabled', 'disabled')
            .attr('title', 'Verifique os dados antes de continuar');
    },
    enable: function () {
        return methods
            .get()
            .removeClass('disabled')
            .prop('disabled', false)
            .attr('title', 'Continuar');
    },
    hide: function () {
        return methods.get().addClass('hide d-none');
    },
    show: function () {
        return methods.get().removeClass('hide d-none');
    },
};
module.exports = methods;

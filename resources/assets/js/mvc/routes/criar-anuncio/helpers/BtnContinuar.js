import $ from 'jquery';

const btnContinuar = {
    get: function () {
        return $('.step-controls .btn-continuar');
    },
    disable: function () {
        return btnContinuar
            .get()
            .addClass('disabled')
            .prop('disabled', 'disabled')
            .attr('title', 'Verifique os dados antes de continuar');
    },
    enable: function () {
        return btnContinuar
            .get()
            .removeClass('disabled')
            .prop('disabled', false)
            .attr('title', 'Continuar');
    },
    hide: function () {
        return btnContinuar.get().addClass('hide d-none');
    },
    show: function () {
        return btnContinuar.get().removeClass('hide d-none');
    },
};
export default btnContinuar;

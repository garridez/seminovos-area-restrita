
var methods = {
    get: function () {
        return $('.step-controls .btn-continuar');
    },
    disable: function () {
        return methods.get()
                .addClass('disabled')
                .attr('disabled', true)
                .attr('title', 'Verifique os dados antes de continuar');
    },
    enable: function () {
        return methods.get()
                .removeClass('disabled')
                .attr('disabled', false)
                .attr('title', 'Continuar');
    }
};
module.exports = methods;
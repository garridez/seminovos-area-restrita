
module.exports = function () {
    var $ = require('jquery');

    $('body').on('change', '[name="idEstado"]', function () {
        var $this = $(this);
        var idEstado = $this.val();
        var cidadesSelect = $this.closest('form').find('[name="idCidade"]');
        var addOption = function (value, html) {
            $('<option>').val(value)
                    .html(html)
                    .attr('disabled', html === '-')
                    .appendTo(cidadesSelect);
        };
        $.ajax({
            url: 'json/cidades.json',
            dataType: 'json',
            data: {
                idEstado: idEstado
            },
            success: function (data) {
                cidadesSelect.html('');
                addOption('', 'Selecione');
                $.each(data, function (i, e) {
                    addOption(e.idCidade, e.cidade);
                });
            }
        });
    });
};

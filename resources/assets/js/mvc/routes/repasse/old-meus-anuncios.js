module.exports.seletor = 'old.c-repasse.a-meus-anuncios';

module.exports.callback = async ($) => {
    $('#excluirModal').on('show.bs.modal', function (event) {
        const modal = $(this);
        const button = $(event.relatedTarget);
        const veiculoId = button.data('id-veiculo');
        const marca = button.data('marca-veiculo');
        const modelo = button.data('modelo-veiculo');
        const email = button.data('email');

        modal.find('.modal-body').text(`Excluir ${marca} ${modelo}?`);

        modal.on('click', '#delete', function () {
            const settingsDelete = {
                url: `https://autoconecta.com.br/api/vehicles/${veiculoId}?email=${email}`,
                method: 'DELETE',
                timeout: 0,
            };

            $.ajax(settingsDelete).done(function () {
                window.location.href = '/repasse/meus-anuncios';
            });
        });
    });
};

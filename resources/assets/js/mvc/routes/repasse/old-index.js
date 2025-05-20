module.exports.seletor = 'old.c-repasse.a-index';

module.exports.callback = async ($) => {
    $('#modalFotos').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const veiculoId = button.data('id-veiculo');
        const modal = $(this);
        var settings = {
            url: `https://autoconecta.com.br/api/vehicles/${veiculoId}`,
            method: 'GET',
            timeout: 0,
        };

        $.ajax(settings).done(function (response) {
            modal
                .find('.modal-title')
                .text(`${response.data.courier_brand} ${response.data.courier_model}`);
            $('#deck-fotos').empty();
            response.data.pictures.map((picture) =>
                $('#deck-fotos').append(`
                <div class="card" style="min-width: 250px">
                    <img src="${picture.full_path}" alt="" class="card-img-top">
                </div>`),
            );
        });
    });

    $('#modalContato').on('show.bs.modal', function (event) {
        const button = $(event.relatedTarget);
        const veiculoId = button.data('id-veiculo');
        const modal = $(this);

        var settings = {
            url: `https://autoconecta.com.br/api/vehicles/${veiculoId}`,
            method: 'GET',
            timeout: 0,
        };

        $.ajax(settings).done(function (response) {
            var data = response.data;
            modal.find('.modal-title').text(`${data.courier_brand} ${data.courier_model}`);

            modal.find('#nome').text(`Nome: ${data.user_details.name}`);

            modal.find('#empresa').text(`Empresa: ${data.user_details.company_name}`);
            modal
                .find('#cidade')
                .text(`Cidade/Estado: ${data.user_details.city}/${data.user_details.state}`);
            modal.find('#telefone').text(`Telefone: ${data.user_details.cellphone1}`);
            modal
                .find('#detalhes')
                .text(
                    data.description
                        ? `Detalhes do Veículo: ${data.description}`
                        : 'Sem detalhes do veículo',
                );
        });
    });
};

$('#modalFotos').on('show.bs.modal', function (event){
    const button = $(event.relatedTarget);
    const veiculoId = button.data('id-veiculo');
    const modal = $(this);
    var settings = {
        "url": `https://autoconecta.com.br/api/vehicles/${veiculoId}`,
        "method": "GET",
        "timeout": 0,
      };
      
      $.ajax(settings).done(function (response) {
        modal.find('.modal-title').text(`${response.data.courier_brand} ${response.data.courier_model}`);
        $('#deck-fotos').empty();
        response.data.pictures.map(picture =>
            $('#deck-fotos').append((`<div class="card" style="min-width: 250px" ><img src="${picture.full_path}" alt="" class="card-img-top"></div>`))
        )
      });

})

$('#modalContato').on('show.bs.modal', function(event){
    const button = $(event.relatedTarget);
    const veiculoId = button.data('id-veiculo');
    const modal = $(this);

    var settings = {
        "url": `https://autoconecta.com.br/api/vehicles/${veiculoId}`,
        "method": "GET",
        "timeout": 0,
      };
      
      $.ajax(settings).done(function (response) {
        modal.find('.modal-title').text(`${response.data.courier_brand} ${response.data.courier_model}`);
        modal.find('#nome').text(`Nome: ${response.data.user_details.name}`);
        modal.find('#empresa').text(`Empresa: ${response.data.user_details.company_name}`);
        modal.find('#cidade').text(`Cidade/Estado: ${response.data.user_details.city}/${response.data.user_details.state}`);
        modal.find('#telefone').text(`Telefone: ${response.data.user_details.cellphone1}`);
        modal.find('#detalhes').text( response.data.description ? `Detalhes do Veículo: ${response.data.description}`: "Sem detalhes do veículo");
    });


})
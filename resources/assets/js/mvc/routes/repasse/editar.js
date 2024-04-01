module.exports.seletor = '.c-repasse.a-editar';
module.exports.callback = async ($) => {
    $("body").on('submit', '#edit', function (e) {

        var email = $("input[name='email']").val();
        var idVeiculo = $("input[name='idVeiculo'").val()
        var placa = $("input[name='placa'").val()
        var marca = $("input[name='marca'").val()
        var modelo = $("input[name='modelo'").val()
        var anoFabrica = $("input[name='anoFabrica'").val()
        var anoModelo = $("input[name='anoModelo'").val()

        e.preventDefault();
        var formEdit = document.getElementById('edit');

        var formDataEdit = new FormData(formEdit);
        formDataEdit.append("email", email);
        formDataEdit.append("license_plate", placa);
        formDataEdit.append("creation_year", anoFabrica);
        formDataEdit.append("model_year", anoModelo);
        formDataEdit.append("courier_value_sale", $("input[name='valor']").val());
        formDataEdit.append("fipe_price", $("input[name='valor']").val());
        formDataEdit.append("courier_brand", marca);
        formDataEdit.append("courier_model", modelo);
        formDataEdit.append("courier_description", $("textarea[name='description']").val());
        formDataEdit.append("_method", "PUT");
        let fileInput = document.querySelector('input[type="file"]');
        if (fileInput.files.length > 0) {
            for (let i = 0; i < fileInput.files.length; i++) {
                formDataEdit.append(`pictures[${i}]`, fileInput.files[i]);
            }
        }


        var settingsEdit = {
            "url": "https://autoconecta.com.br/api/vehicles/" + idVeiculo,
            "method": "POST",
            "timeout": 0,
            "headers": {
                "Accept": "application/json",
            },
            "processData": false,
            "mimeType": "multipart/form-data",
            "contentType": false,
            "data": formDataEdit
        };

        $.ajax(settingsEdit).done(function (response) {
            window.location.href = '/repasse/meus-anuncios'
        });

    })
    $(document).on('click', '#sold', function () {
        var email = $("input[name='email']").val();
        var idVeiculo = $("input[name='idVeiculo'").val()
        var settingsSold = {
            "url": `https://autoconecta.com.br/api/vehicles/${idVeiculo}/mark-as-sold?email=${email}&status=2`,
            "method": "PATCH",
            "timeout": 0,
            "headers": {
                "Accept": "application/json",
            },
            "processData": false,
            "mimeType": "multipart/form-data",
            "contentType": false,
        };

        $.ajax(settingsSold).done(function (response) {
            console.log(response);
        });
    });
    $(document).on('click', '#delete', function () {
        var email = $("input[name='email']").val();
        var idVeiculo = $("input[name='idVeiculo'").val()
        var settingsDelete = {
            "url": `https://autoconecta.com.br/api/vehicles/${idVeiculo}?email=${email}`,
            "method": "DELETE",
            "timeout": 0,
        };

        $.ajax(settingsDelete).done(function (response) {
            window.location.href = '/repasse/meus-anuncios'
        });
    })
}

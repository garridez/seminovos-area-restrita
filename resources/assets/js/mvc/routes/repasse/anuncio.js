module.exports.seletor = '.c-repasse.a-anuncio';
module.exports.callback = async ($) => {
    $("input[name='license-plate']").keyup(function () {
        licensePlate = $("input[name='license-plate']").val();
        if (licensePlate.length == 7) {
            var settings = {
                "url": "/repasse/license-plate?&license-plate=" + licensePlate,
                "method": "GET",
                "timeout": 0,
            };

            $.ajax(settings).done(function (response) {
                if (response.status == 200) {
                    $(".table-license-plate-api-brand").text(response.data[0].historicoCarro.dados_veiculo.marca);
                    $(".table-license-plate-api-model").text(response.data[0].historicoCarro.dados_veiculo.modelo);
                    $(".table-license-plate-api-color").text(response.data[0].historicoCarro.dados_veiculo.cor);
                    $(".table-license-plate-api-fuel").text(response.data[0].historicoCarro.dados_veiculo.combustivel);
                    $(".table-license-plate-api-city").text(response.data[0].historicoCarro.dados_veiculo.cidade);
                    $(".table-license-plate-api-state").text(response.data[0].historicoCarro.dados_veiculo.estado);
                    if(!response.data[0].historicoCarro.fipe){
                        $(".table-license-plate-api-fipe-price").text("Indisponível");
                    }
                    else{
                        $(".table-license-plate-api-fipe-price").text(response.data[0].historicoCarro.fipe.valor_fipe);
                    }
                    $(".table-license-plate-api").show();

                    email = $("input[name='email']").val();
                    marca = response.data[0].historicoCarro.dados_veiculo.marca;
                    modelo = response.data[0].historicoCarro.dados_veiculo.modelo;
                    anoModelo = response.data[0].historicoCarro.dados_veiculo.ano_modelo;
                    anoFabrica = response.data[0].historicoCarro.dados_veiculo.ano_fabricacao;
                    if(!response.data[0].historicoCarro.fipe){
                        valorFipe = 0;
                        numeroFipe = 0;
                    }
                    else{
                        valorFipe = response.data[0].historicoCarro.fipe.valor_fipe;
                        numero = valorFipe.replace("R$ ", "").replace(".", "").replace(",", ".");
                        numeroFipe = parseFloat(numero);
                    }
                }
            });
        }
    });

    $("body").on('submit', '#save', function (e) {

        e.preventDefault();
        var form = document.getElementById('save');

        var formData = new FormData(form);
        formData.append("email", email);
        formData.append("license_plate", $("input[name='license-plate']").val());
        formData.append("creation_year", anoFabrica);
        formData.append("model_year", anoModelo);
        formData.append("fipe_price", numeroFipe);
        formData.append("courier_value_sale", $("input[name='valor']").val());
        formData.append("courier_brand", marca);
        formData.append("courier_model", modelo);
        formData.append("courier_description", $("textarea[name='description']").val());
        let fileInput = document.querySelector('input[type="file"]');
        if (fileInput.files.length > 0) {
            for (let i = 0; i < fileInput.files.length; i++) {
                formData.append(`pictures[${i}]`, fileInput.files[i]);
            }
        }

        var settingsPost = {
            "url": "https://autoconecta.com.br/api/vehicles",
            "method": "POST",
            "timeout": 0,
            "headers": {
                "Accept": "application/json",
            },
            "processData": false,
            "mimeType": "multipart/form-data",
            "contentType": false,
            "data": formData
        };

        $.ajax(settingsPost).done(function (response) {
            window.location.href = '/repasse'
        });

    });
}


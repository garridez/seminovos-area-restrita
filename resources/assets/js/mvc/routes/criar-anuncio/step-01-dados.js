module.exports.seletor = '.c-criar-anuncio.a-index';

module.exports.callback = ($) => {
    $('.step-container').on('steps-loaded', init);
};

function init() {
    var ctx = $('.step-dados');
    var anoFabricacao = ctx.find('[name="anoFabricacao"]');
    var tipo = $('input[name="tipoCadastro"]');
    var marca = ctx.find('[name="idMarca"]');
    var modelo = ctx.find('[name="modeloCarro"]');
    var anoFabricacaoOptions = anoFabricacao.find('option');
    var anoModelo = ctx.find('[name="anoModelo"]');
    var anoModeloOptions = anoModelo.find('option');
    var getValInt = function (element) {
        var val = parseInt($(element).val(), 10);
        if (Number.isNaN(val)) {
            return false;
        }
        return val;
    };
    anoFabricacao.change(function () {
        var anoF = getValInt(this);
        var anoModeloOptionsFiltred = anoModeloOptions;
        if (anoF !== false) {
            anoModeloOptionsFiltred = anoModeloOptions
                    .filter(function () {
                        var anoM = getValInt(this);
                        if (anoM === false) {
                            return true;
                        }
                        return anoF === anoM || (anoF === anoM - 1);
                    });
        }
        anoModelo.html('')
                .prepend(anoModeloOptionsFiltred)
                .val('');

    });
    
    anoModelo.change(function(){
       
       $.ajax({
                type: "POST",
                url: "/carro/versao",
                data: {
                    idTipo: getValInt(tipo),
                    idMarca: getValInt(marca),
                    idModelo: getValInt(modelo),
                    anoModelo: getValInt(this)
                },
                dataType: "json",
                success: function (response) {
                    $('[name="versao"]').empty();
                    $('[name="versao"]').append('<option>Selecione a versao</option>');
                    var dados = response.data;

                    for (var i = 0; i < dados.length; i++) {
                        //Use the Option() constructor to create a new HTMLOptionElement.
                        var option = new Option(dados[i].modelo, dados[i].modelo);
                        //Convert the HTMLOptionElement into a JQuery object that can be used with the append method.
                        $(option).html(dados[i].modelo);
                        //Append the option to our Select element.
                        $('[name="versao"]').append(option);
                    }

                },
                error: function (e) {
                    if (e.responseJSON) {
                        HandleApiError(e.responseJSON);
                    } else {
                        HandleApiError(false);
                    }
                }
            });
       
    });
}
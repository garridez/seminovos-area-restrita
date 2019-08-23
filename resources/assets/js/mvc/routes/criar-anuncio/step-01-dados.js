module.exports.seletor = '.c-criar-anuncio.a-index';

module.exports.callback = ($) => {
    $('.step-container').on('steps-loaded', init);
};

function init() {
    var Confirms = require("components/Confirms")
    var ctx = $('.step-dados');
    var anoFabricacao = ctx.find('[name="anoFabricacao"]');
    var tipo = $('input[name="tipoCadastro"]');
    var marca = ctx.find('[name="idMarca"]');
    var modelo = ctx.find('[name="modeloCarro"]');
    var anoFabricacaoOptions = anoFabricacao.find('option');
    var anoModelo = ctx.find('[name="anoModelo"]');
    var versao = ctx.find('[name="versao"]');
    var anoModeloOptions = anoModelo.find('option');
    var motorSelect = ctx.find('[name="motor"]');
    var motorOptions = motorSelect.find('option');
    var portasSelect = ctx.find('[name="portas"]');
    var portasOptions = portasSelect.find('option');
    var valvulasSelect = ctx.find('[name="idValvula"]');
    var valvulasOptions = valvulasSelect.find('option');
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
    
    versao.change(function () {
        //console.log($(this).find('option:selected').val());
        $('[name="codFipe"]').val('');
        
        if($(this).find('option:selected').val() == 99){
            $('#divOutraVersao').removeClass("hide");
            //$('[name="codFipe"]').empty();
            return;
        }else{
            $('#divOutraVersao').addClass("hide");
        }
        
        var itens = $(this).find('option:selected').data('itens');
        
        $('[name="codFipe"]').val(itens['codFipe']);
        
        if(typeof itens['portas'] !== 'undefined'){
            $('[name="portas"]').empty();
            $('[name="portas"]').append('<option value="">Selecione</option>');

            for (var i = 0; i < itens['portas'].length; i++) {

                var option = $(new Option(itens['portas'][i].id, itens['portas'][i].id));

                option.html(itens['portas'][i].portas);

                $('[name="portas"]').append(option);
                
                if(itens['portas'].length == 1){
                    $('[name="portas"]').val(itens['portas'][i].id);
                }
            }
            
        }else{
            portasSelect.html('')
                .prepend(portasOptions)
                .val('');
        }
        
        if(typeof itens['motor'] !== 'undefined'){
        
            $('[name="motor"]').empty();
            $('[name="motor"]').append('<option value="">Selecione</option>');

            for (var i = 0; i < itens['motor'].length; i++) {

                var option = $(new Option(itens['motor'][i].id, itens['motor'][i].id));

                option.html(itens['motor'][i].motor);

                $('[name="motor"]').append(option);
                
                if(itens['motor'].length == 1){
                    $('[name="motor"]').val(itens['motor'][i].id);
                }
            }
            
        }else{
            motorSelect.html('')
                .prepend(motorOptions)
                .val('');
        }
        
        if(typeof itens["valvulas"] !== 'undefined'){
            $('[name="idValvula"]').empty();
            $('[name="idValvula"]').append('<option value="">Selecione</option>');

            for (var i = 0; i < itens['valvulas'].length; i++) {

                var option = $(new Option(itens["valvulas"][i].id, itens["valvulas"][i].id));

                option.html(itens["valvulas"][i].valvulas);

                $('[name="idValvula"]').append(option);
                
                if(itens['valvulas'].length == 1){
                    $('[name="idValvula"]').val(itens["valvulas"][i].id);
                }
            }
            
        }else{
            valvulasSelect.html('')
                .prepend(valvulasOptions)
                .val('');
        }
        
    });
    
    anoModelo.change(function(){
        $('#divOutraVersao').addClass("hide");
        portasSelect.html('')
                .prepend(portasOptions)
                .val('');
        motorSelect.html('')
                .prepend(motorOptions)
                .val('');
        valvulasSelect.html('')
                .prepend(valvulasOptions)
                .val('');
        
       
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
                    $('[name="versao"]').append('<option value="">Selecione a versao</option>');
                    var dados = response.data;

                    for (var i = 0; i < dados.length; i++) {
                        //Use the Option() constructor to create a new HTMLOptionElement.
                        var option = $(new Option(dados[i].considerada, dados[i].considerada));
                        //Convert the HTMLOptionElement into a JQuery object that can be used with the append method.
                        option
                                .html(dados[i].versao)
                                .data('itens', dados[i].itens)
                            ;
                        //Append the option to our Select element.
                        $('[name="versao"]').append(option);
                        
                    }
                    
                    $('[name="versao"]').append("<option value='99'>Outra versão</option>")
                    
                    if(dados.length == 0){
                        $('[name="versao"] option[value=99]').attr('selected','selected').change();
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
    $(".combinar-valor").change(function(event){
        event.preventDefault();
        var $check = $(this).find("input[type='checkbox']");
        if($check.is(":checked")){
            Confirms.warning({
                text:"Não exibindo o valor do anúncio os possíveis compradores não poderam ver o valor anunciado",
                title:$("<span>").html("Deseja <span class='text-primary'>NÃO EXIBIR O VALOR</span> do anúncio?"),
                confirmCallback: function(){
                    $check.prop('checked', true);
                    $(".modal").modal('hide');
                },
                negateCallback:function(){
                    $check.prop('checked', false);
                }
            })
        }
    })
}
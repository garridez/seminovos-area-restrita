module.exports.seletor = '.c-criar-anuncio.a-index';
function stopEvent(e) {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    return false;
}

var DataLayerGTMPopulate = require('helpers/DataLayerGTMPopulate');
window.AutoFillCriarAnuncio = require('helpers/AutoFillCriarAnuncio');
var Alert = require('components/Alerts');
import HandleApiError from '../../../components/HandleApiError';

module.exports.callback = ($) => {
    $('.step-container').on('steps-loaded', init);
    $('.step-container').on('step:pre-exit:dados', function () {
        if ($('#dados-basicos #flagCriando').val() == 1) {
            var ctx = $('.step-0, .step-1');
            DataLayerGTMPopulate(ctx, 'checkout_step_1');
        }

        var anoVeiculo = parseInt($('.step-dados').find('select[name="anoModelo"]').val()) || 0;
        var anoAtual = new Date().getFullYear();
        var stepPreco = $('.step-preco');
        if (anoAtual - anoVeiculo <= 15) {
            stepPreco.find('.control-financiamento').removeClass('d-none');
        }
    });
    $('.step-container').on('step:pre-exit:preco', function () {
        if ($('#dados-basicos #flagCriando').val() == 1) {
            var ctx = $('.step-0, .step-1');
            DataLayerGTMPopulate(ctx, 'checkout_step_2');
        }
    });
    $('.step-container').on('step:pre-exit:mais-informacoes', function () {
        if ($('#dados-basicos #flagCriando').val() == 1) {
            var ctx = $('.step-0, .step-1');
            DataLayerGTMPopulate(ctx, 'checkout_step_3');
        }
    });
};

function init() {
    var Confirms = require('components/Confirms');
    var ctx = $('.step-dados');
    var veiculoZeroKm = ctx.find('[name="veiculo_zero_km"]');
    var motoTrilha = ctx.find('[name="motoTrilha"]');
    var placa = ctx.find('[name="placa"]');
    var anoFabricacao = ctx.find('[name="anoFabricacao"]');
    var tipo = $('input[name="tipoCadastro"]');
    var tipoCadastro = $('input[name="tipoUsuarioCadastro"]').val();
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
    var combustivelSelect = ctx.find('[name="combustivel"]');
    var combustivelOptions = combustivelSelect.find('option');
    var getValInt = function (element) {
        var val = parseInt($(element).val(), 10);
        if (Number.isNaN(val)) {
            return false;
        }
        return val;
    };

    if ($('#dados-basicos .idVeiculo').val() == '') {
        $('#dados-basicos #flagCriando').val(1);
    }

    if ($('#dados-basicos .idVeiculo').val() !== '') {
        var camposToDisable = ['placa', 'veiculo_zero_km', 'idMarca', 'modeloCarro'];
        let $ctx = $('[name="form_dadosVeiculo"]');
        camposToDisable.forEach(function (element) {
            $ctx.find('[name="' + element + '"]')
                .addClass('disabled')
                .attr('disabled', true)
                .attr('required', false)
                .closest('.form-group-default')
                .removeClass('required');
        });
    }

    placa.on('change', function () {
        anoModelo.html('').prepend(anoModeloOptions).val('');
    });
    anoFabricacao.on('change', function () {
        var anoF = getValInt(this);
        var anoModeloOptionsFiltred = anoModeloOptions;
        if (anoF !== false) {
            anoModeloOptionsFiltred = anoModeloOptions.filter(function () {
                var anoM = getValInt(this);
                if (anoM === false) {
                    return true;
                }
                return anoF === anoM || anoF === anoM - 1;
            });
        }
        anoModelo.html('').prepend(anoModeloOptionsFiltred).val('');
    });

    versao.on('change', function () {
        //console.log($(this).find('option:selected').val());
        $('[name="codFipe"]').val('');

        if ($(this).find('option:selected').val() == -1) {
            $('#divOutraVersao').removeClass('hide');
            //$('[name="outraVersao"]').prop('required',true); // Esse campo não pode ser obrigatório
            //$('[name="codFipe"]').empty();
            return;
        } else {
            $('#divOutraVersao').addClass('hide');
            $('[name="outraVersao"]').val('');
            $('[name="outraVersao"]').prop('required', false);
        }

        var itens = $(this).find('option:selected').data('itens');

        $('[name="codFipe"]').val(itens['codFipe']);

        if (typeof itens['portas'] !== 'undefined') {
            $('[name="portas"]').empty();
            $('[name="portas"]').append('<option value="">Selecione</option>');

            for (var i = 0; i < itens['portas'].length; i++) {
                var option = $(new Option(itens['portas'][i].id, itens['portas'][i].id));

                option.html(itens['portas'][i].portas);

                $('[name="portas"]').append(option);

                if (itens['portas'].length == 1) {
                    $('[name="portas"]').val(itens['portas'][i].id);
                }
            }
        } else {
            portasSelect.html('').prepend(portasOptions).val('');
        }

        if (typeof itens['motor'] !== 'undefined') {
            $('[name="motor"]').empty();
            $('[name="motor"]').append('<option value="">Selecione</option>');

            for (let i = 0; i < itens['motor'].length; i++) {
                let option = $(new Option(itens['motor'][i].id, itens['motor'][i].id));

                option.html(itens['motor'][i].motor);

                $('[name="motor"]').append(option);

                if (itens['motor'].length == 1) {
                    $('[name="motor"]').val(itens['motor'][i].id);
                }
            }
        } else {
            motorSelect.html('').prepend(motorOptions).val('');
        }

        if (typeof itens['valvulas'] !== 'undefined') {
            $('[name="idValvula"]').empty();
            $('[name="idValvula"]').append('<option value="">Selecione</option>');

            for (let i = 0; i < itens['valvulas'].length; i++) {
                let option = $(new Option(itens['valvulas'][i].id, itens['valvulas'][i].id));

                option.html(itens['valvulas'][i].valvulas);

                $('[name="idValvula"]').append(option);

                if (itens['valvulas'].length == 1) {
                    $('[name="idValvula"]').val(itens['valvulas'][i].id);
                }
            }
        } else {
            valvulasSelect.html('').prepend(valvulasOptions).val('');
        }
    });

    anoModelo.change(function (event, limparCampos = true, caracteristica = '') {
        $('#divOutraVersao').addClass('hide');

        if (!getValInt(this)) {
            return stopEvent(event);
        }

        if (limparCampos) {
            portasSelect.html('').prepend(portasOptions).val('');
            motorSelect.html('').prepend(motorOptions).val('');
            valvulasSelect.html('').prepend(valvulasOptions).val('');
        }

        $.ajax({
            type: 'POST',
            url: '/carro/versao',
            data: {
                idTipo: getValInt(tipo),
                idMarca: getValInt(marca),
                idModelo: getValInt(modelo),
                anoModelo: getValInt(this),
            },
            dataType: 'json',
            success: function (response) {
                $('[name="versao"]').empty();
                $('[name="versao"]').append('<option value="">Selecione a versao</option>');
                var dados = response.data;
                var selecionadoVersao = false;

                for (var i = 0; i < dados.length; i++) {
                    //Use the Option() constructor to create a new HTMLOptionElement.
                    var option = $(new Option(dados[i].considerada, dados[i].considerada));
                    //Convert the HTMLOptionElement into a JQuery object that can be used with the append method.
                    option.html(dados[i].versao).data('itens', dados[i].itens);
                    if (caracteristica == dados[i].considerada) {
                        selecionadoVersao = true;
                        $('[name="versao"]').append($(option).attr('selected', 'selected'));
                    } else {
                        //Append the option to our Select element.
                        $('[name="versao"]').append(option);
                    }
                }

                if (!selecionadoVersao && caracteristica != '') {
                    $('[name="versao"]').append(
                        '<option value="-1" selected>Outra versão</option>',
                    );
                    $('[name="versao"]').trigger('change');
                    $('[name="outraVersao"]').val(caracteristica);
                    if (tipoCadastro == '1') $('[name="outraVersao"]').prop('readonly', false);
                    else $('[name="outraVersao"]').prop('readonly', true);
                } else {
                    $('[name="versao"]').append('<option value="-1">Outra versão</option>');
                }

                if (dados.length == 0) {
                    $('[name="versao"]').val('-1').change();
                }
            },
            error: function (e) {
                if (e.responseJSON) {
                    HandleApiError(e.responseJSON);
                } else {
                    HandleApiError(false);
                }
            },
        });
    });

    // executa comando para preencher versao e envia as caracteristicas
    // que usuario selecionou no cadastro
    anoModelo.trigger('change', [false, $('[name="caracteristicaVeiculo"]').val()]);

    $('.combinar-valor').on('change', function (event) {
        event.preventDefault();
        var $check = $(this).find('input[type="checkbox"]');
        if ($check.is(':checked')) {
            Confirms.warning({
                text: 'Não exibindo o valor do anúncio os possíveis compradores não poderam ver o valor anunciado',
                title: $('<span>').html(
                    'Deseja <span class="text-primary">NÃO EXIBIR O VALOR</span> do anúncio?',
                ),
                confirmCallback: function () {
                    $check.prop('checked', true);
                    $('.modal').modal('hide');
                },
                negateCallback: function () {
                    $check.prop('checked', false);
                },
            });
        }
    });

    veiculoZeroKm.on('click', function () {
        if ($(this).is(':checked')) {
            placa.removeAttr('required');
            placa.closest('.form-group').removeClass('required');
            placa.closest('.placa-container').removeClass('is-invalid').addClass('is-valid');
            placa.val('');
            Alert.info(
                '<h5>Veículos marcados como 0km irão aparecer como 0km mesmo se for informada a quilometragem!</h5>',
                'Atenção',
            );
        } else {
            if (!motoTrilha.is(':checked')) {
                placa.attr('required', true);
                placa.closest('.form-group').addClass('required');
                placa.closest('.placa-container').removeClass('is-invalid is-valid');
                placa.val('');
            }
        }
    });

    motoTrilha.on('change', function () {
        if ($(this).is(':checked')) {
            placa.removeAttr('required');
            placa.closest('.form-group').removeClass('required');

            $('select[name="idMarca"] option').addClass('hide');
            let marcasTrilha = [95, 115, 103, 97, 173, 101, 203];
            marcasTrilha.forEach((element) => {
                marca.find(`option[value='${element}']:not([data='destaque'])`).removeClass('hide');
            });
        } else {
            if (!veiculoZeroKm.is(':checked')) {
                placa.attr('required', true);
                placa.closest('.form-group').addClass('required');
            }
            $('select[name="idMarca"] option').removeClass('hide');
        }
    });

    combustivelSelect.on('change', function () {
        console.log($(this).find('option:selected').val());
        if ($(this).find('option:selected').val() === '11') {
            console.log($(this).find('option:selected').val());
            motorSelect.removeAttr('required');
            motorSelect.closest('.form-group').removeClass('required');
        }
    });

    $('body').on('change', $('select[name="modeloCarro"]'), function () {
        if ($('select[name="idMarca"] option:selected').val() != '') {
            if ($('input[name="motoTrilha"]').is(':checked')) {
                //honda 95 - yamaha 115 - ktm 103 - Kawasaki 101 -MXF 203
                if (
                    marca.val() == 95 ||
                    marca.val() == 115 ||
                    marca.val() == 103 ||
                    marca.val() == 101 ||
                    marca.val() == 203
                ) {
                    $('select[name="modeloCarro"] option').addClass('hide');
                    let modelosTrilha = [
                        673, 1095, 1507, 653, 2122, 668, 838, 1945, 1609, 2241, 912, 724, 1716,
                        1402, 2237, 2051, 2146, 2372, 2373, 2391,
                    ];

                    modelosTrilha.forEach((element) => {
                        modelo.find(`option[value='${element}']`).removeClass('hide');
                    });
                }
            }
        }
    });

    var $ctx = $('form[name="form_opcionaisVeiculo"]');
    /* IMPLEMENTAÇÃO DA OPÇÃO DE ATALHO PARA MARCAR OS ACESSÓRIOS DE UM CARRO COMPLETO*/
    $ctx.find('#btnCompleto').on('click', function () {
        var checked = $(this).find('#completoCheckbox').is(':checked');
        let acessorios = [4, 6, 7, 17, 33, 35];
        acessorios.forEach((element) => {
            $('#dadosAcessorios').find(`input[value='${element}']`).prop('checked', checked);
        });
    });

    $ctx.find('.airbags select').on('change', function (e) {
        $(this).removeClass('selected');
        if ($(this).val() == '') {
            return;
        }
        $(this).addClass('selected');
    });
}

/**
 * Este script junta os dados do form, 'dados', 'preco' e 'mais-informacoes'
 */
module.exports.seletor = '.c-criar-anuncio.a-index';
function stopEvent(e) {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    return false;
}
module.exports.callback = ($) => {
    require('components/StepPlugin');
    var loading = require('components/Loading');
    var HandleApiError = require('components/HandleApiError');
    var marcaModelo = require('components/MarcaModelo');
    var BtnContinuar = require('./helpers/BtnContinuar');
    var GetUrl = require('./helpers/GetUrl');

    var advancedAlerts = require('components/AdvancedAlerts');

    var stepsContainer = $('.step-container.step-veiculo');
    var lastSavedData;
    var dataWithError;
    var formWithError;

    //($('#form_dadosVeiculo'))
    $('.anuncio-steps')
        .on('steps-loaded', function () {
            // Para esperar as máscaras serem aplicadas
            setTimeout(function () {
                let idStatus = $("input.idStatus").val();
                if (idStatus == 3 || idStatus == 6 || idStatus == 10) {
                    $("input[name='placa']").prop('readonly', false).prop('disabled', false);
                }
                lastSavedData = $('form', '#dados-basicos,.step-dados,.step-preco,.step-mais-informacoes').serialize();
            }, 500);
        })
        .on('steps-loaded', function () {
            marcaModelo($('#form_dadosVeiculo'));
            var placaAtual = $('#placaVeiculo').val();
            /* @todo COLOCAR A FUNÇÃO DE VALIDAR PLACA DURANTE O TAB */
            $("form[name='form_dadosVeiculo']").find("input[name='placa']").blur(function (event) {

                var placaInput = $(this);
                var placa = placaInput.val() || '';
                if (!placa || placa.length < 7) {
                    return;
                }
                if(placaAtual.toUpperCase() == placa.toUpperCase()) {
                    return;
                }
                BtnContinuar.disable();
                $.ajax({
                    type: "GET",
                    url: "/carro/placa-disponivel/"+placa.toLowerCase(),
                    dataType: "json",
                    success: function (response) {
                        placaInput
                            .parent()
                            .removeClass('is-invalid is-valid')
                            .addClass(response.placaDisponivel ? 'is-valid' : 'is-invalid');
                        if (!response.placaDisponivel) {
                            BtnContinuar.disable();
                            advancedAlerts.error({
                                title: "Placa já cadastrada",
                                text: "Placa já cadastrada no sistema, confira a placa ou entre em cotato.",
                                time: 10000
                            })
                            return;
                        }

                        if(response.historicoCarro) {
                            
                            let historico = response.historicoCarro;
                            let anoModelo = historico.dados_veiculo.ano_modelo;
                            
                            //seta ano de fabricacao
                            $("select[name='anoFabricacao']").val(historico.dados_veiculo.ano_fabricacao);
                            $("select[name='anoFabricacao'] option:selected").prop('disabled', false).removeClass("hide");
                            $("select[name='anoFabricacao'] option:not(:selected)").prop('disabled', true).addClass("hide");

                            //seta ano do modelo
                            if(anoModelo === '0'){
                                anoModelo = historico.dados_veiculo.ano_fabricacao;
                            }
                            $("select[name='anoModelo']").val(anoModelo);
                            $("select[name='anoModelo'] option:selected").prop('disabled', false).removeClass("hide");
                            $("select[name='anoModelo'] option:not(:selected)").prop('disabled', true).addClass("hide");

                            //trigger para buscar versao
                            setTimeout(() => {
                                $("select[name='anoModelo']").trigger('change', [ false, $('[name="caracteristicaVeiculo"]').val() ]);
                            },0); 

                            //seta cor do veiculo
                            let corSelecionada = historico.dados_veiculo.cor.toLowerCase().slice(0, -1);
                            $("select[name='cor'] option:selected").prop('selected', false);
                            let options = $("select[name='cor'] option");
                            options.each(function(k, v) {
                                let option = $(v);
                                let cor = option.val().toLowerCase().slice(0, -1); 
                                if(corSelecionada == cor) {
                                    option.prop('selected', true);
                                    return false;
                                }
                            });

                            //seta cobustivel -- precisa ser verificado
                            $("select[name='combustivel'] option:selected").prop('selected', false);
                            let combustivelSelecionado = historico.dados_veiculo.combustivel;
                            options = $("select[name='combustivel'] option");
                            options.each(function(k, v) {
                                let option = $(v);
                                combustivel = option.html().trim();
                                if(combustivel == combustivelSelecionado) {
                                    option.prop('selected', true);
                                    return false;
                                }
                            });


                            //seta marca -- precisa ser verificado
                            $("select[name='idMarca'] option:not(:selected)").prop('disabled', false).removeClass("hide");
                            $("select[name='idMarca'] option:selected").prop('selected', false);
                            options = $("select[name='idMarca'] option"); 
                            let marcaSelecionada = historico.dados_veiculo.marca.toLowerCase();
                            options.each(function(k, v) {
                                let option = $(v);
                                let marca = option.html().trim().toLowerCase();
                                if( marca == marcaSelecionada) {
                                    option.prop('selected', true);
                                    $("select[name='idMarca']").trigger('change');
                                    $("select[name='idMarca'] option:selected").prop('disabled', false).removeClass("hide");
                                    $("select[name='idMarca'] option:not(:selected)").prop('disabled', true).addClass("hide");
                                    return false;
                                }
                            });

                            //seta o modelo
                            let modeloSelecionado = historico.dados_veiculo.modelo;
                            $("select[name='modeloCarro'] option:selected").prop('selected', false);
                            options = $("select[name='modeloCarro'] option"); 
                            var matchRegex = -1;
                            options.each(function(k, v) {
                                let option = $(v);
                                let modelo = option.html().trim();
                                let regex = RegExp(modelo, 'i'); 
                                if(regex.test(modeloSelecionado) && modelo != '') {
                                    
                                    if(matchRegex > -1) {
                                        previosOption = $(options[matchRegex]).html().trim();
                                        matchRegex =  previosOption.length > modelo.length ? matchRegex : k;
                                    }else {
                                        matchRegex = k
                                    }
                                }
                            });
                            if(matchRegex > -1) {
                                $(options[matchRegex]).prop('selected', true);
                            }

                        }

                        BtnContinuar.enable();
                    },
                    error: function (e) {

                    }
                });

            });

            /* IMPLEMENTAÇÃO DA OPÇÃO DE ATALHO PARA MARCAR OS ACESSÓRIOS DE UM CARRO COMPLETO*/
            $("form[name='form_dadosVeiculo']").find("#btnCompleto").click(function (event) {
                var checked = $(this).find('#completoCheckbox').is(':checked');
                let acessorios = [4, 6, 7, 17, 33, 35];
                acessorios.forEach((element, index) => {
                    $("#dadosAcessorios").find(`input[value='${element}']`).prop('checked', checked);
                });
            });
        }).on('change', function () {
            BtnContinuar.enable();
        });

    var ajaxProcessing = false;
    stepsContainer.on('step:pre-change:mais-informacoes', function (e) {
        var form = $('form', '#dados-basicos,.step-dados,.step-preco,.step-mais-informacoes');
        var dataSerialized = form.serialize();
        if (formWithError === true && dataSerialized === dataWithError) {
            BtnContinuar.disable();
        } else {
            BtnContinuar.enable();
        }
    });
    stepsContainer.on('step:pre-exit:mais-informacoes', function (e) {
        var aceitaProposta = $('input[name="aceitaProposta"]').is(':checked');
        var aceitaLigacao = $('input[name="aceitaLigacao"]').is(':checked');
        var aceitaChat = $('input[name="aceitaChat"]').is(':checked');
        var tipoCadastro = $('input[name="tipoCadastro"]').val();
        
        if(!aceitaProposta && !aceitaLigacao && !aceitaChat && tipoCad == 2){
            advancedAlerts.warning({
                text:'Você precisa selecionar pelo menos um meio para contato',
                title:$('<span class="text-primary">').html('Atenção!')
            });
            return stopEvent(e);
        }

        BtnContinuar.enable();
        if (ajaxProcessing) {
            return stopEvent(e);
        }
        ajaxProcessing = true;

        var formInfo = $('.step-mais-informacoes form');
        formInfo.find('[type="submit"]').first().click();
        if (!formInfo.get(0).checkValidity()) {
            ajaxProcessing = false;
            return stopEvent(e);
        }

        // Salvar todo o formulario anterior as fotos aqui
        var form = $('form', '#dados-basicos,.step-dados,.step-preco,.step-mais-informacoes');
        var dataSerialized = form.serialize();

        if (formWithError && dataSerialized === dataWithError) {
            ajaxProcessing = false;
            return stopEvent(e);
        }
        if (!dataSerialized || dataSerialized === lastSavedData) {
            ajaxProcessing = false;
            return;
        }
        loading.addFeedbackTexts([
            'Salvando dados do veículo...',
            'Salvando os acessórios...',
            'Salvando...',
        ]);
        $.ajax({
            type: "POST",

            /**
             * @TODO Corrigir o "/carro" para o valor correto
             */
            url: "/carro/dados",
            data: dataSerialized,
            dataType: "json",
            success: function (data) {
                ajaxProcessing = false;
                /**
                 * Atribui o valor do veiculo no form, caso a pessoa volte e edite,
                 *      na hora de salvar é enviado o id do veículo e assim é feita a edição
                 */
                data = data.data;
                if (data) {
                    var idVeiculo = data[0].idVeiculo;
                    $('#dados-basicos .idVeiculo').val(idVeiculo);
                    $('#dados-basicos .idAnuncioVeiculo').val(data[0].idAnuncio);

                    var path = window.location.pathname.match(/^\/[a-z]+/).input + '/' + idVeiculo;
                    window.history.pushState(null, null, path);
                }
                // Guarda o que foi serializado para garantir que não vai salvar dados que não foram alterados
                lastSavedData = form.serialize();
                stepsContainer.stepPlugin('next');
                formWithError = false;

            },
            error: function (e) {
                ajaxProcessing = false;
                formWithError = true;
                dataWithError = form.serialize();
                stepsContainer.stepPlugin('goTo', '.step-dados');
                if (e.responseJSON) {
                    HandleApiError(e.responseJSON);
                } else {
                    HandleApiError(false);
                }
            }
        });
        return stopEvent(e);
    });
};
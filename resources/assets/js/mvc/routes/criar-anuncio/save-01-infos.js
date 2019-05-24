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
    var HandleApiError = require('components/HandleApiError');
    var marcaModelo = require('components/MarcaModelo');

    var stepsContainer = $('.step-container.step-veiculo');
    var lastSavedData;
    var dataWithError;
    var formWithError;

    //($('#form_dadosVeiculo'))
    $('.anuncio-steps').on('steps-loaded', function () {
        marcaModelo($('#form_dadosVeiculo'));
        /* IMPLEMENTAÇÃO DA OPÇÃO DE ATALHO PARA MARCAR OS ACESSÓRIOS DE UM CARRO COMPLETO*/
        $("form[name='form_dadosVeiculo']").find("#btnCompleto").click(function (event) {
            var checked = $(this).find('#completoCheckbox').is(':checked');
            let acessorios = [4, 6, 7, 17, 33, 35];
            acessorios.forEach((element, index) => {
                $("#dadosAcessorios").find(`input[value='${element}']`).prop('checked', checked);
            });
        });
    });


    stepsContainer.on('step:pre-exit:mais-informacoes', function (e) {

        var formInfo = $('.step-mais-informacoes form');
        formInfo.find('[type="submit"]').first().click();
        if (!formInfo.get(0).checkValidity()) {
            return stopEvent(e);;
        }

        // Salvar todo o formulario anterior as fotos aqui
        var form = $('form', '#dados-basicos,.step-dados,.step-preco,.step-mais-informacoes');
        var dataSerialized = form.serialize();

        if (formWithError && dataSerialized === dataWithError) {
            return;
        }
        if (!dataSerialized || dataSerialized === lastSavedData) {
            return;
        }

        $.ajax({
            type: "POST",

            /**
             * @TODO Corrigir o "/carro" para o valor correto
             */
            url: "/carro/dados",
            data: dataSerialized,
            dataType: "json",
            success: function (data) {
                /**
                 * Atribui o valor do veiculo no form, caso a pessoa volte e edite,
                 *      na hora de salvar é enviado o id do veículo e assim é feita a edição
                 */
                if (data.data) {
                    $('#dados-basicos .idVeiculo').val(data.data[0].idVeiculo);
                    $('#dados-basicos .idAnuncioVeiculo').val(data.data[0].idAnuncio);
                }

                // Guarda o que foi serializado para garantir que não vai salvar dados que não foram alterados
                lastSavedData = form.serialize();
                stepsContainer.stepPlugin('next');
                formWithError = false;

            },
            error: function (e) {
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
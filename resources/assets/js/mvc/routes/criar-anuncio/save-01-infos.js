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

    //($('#form_dadosVeiculo'))
    $('.anuncio-steps').on('steps-loaded', function () {
        marcaModelo($('#form_dadosVeiculo'));
    });

    stepsContainer.on('step:pre-exit:mais-informacoes', function (e) {

        // Salvar todo o formulario anterior as fotos aqui
        var form = $('form', '#dados-basicos,.step-dados,.step-preco,.step-mais-informacoes');
        var dataSerialized = form.serialize();

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

            },
            error: function (e) {
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
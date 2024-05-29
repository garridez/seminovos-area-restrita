module.exports.seletor = '.c-xml.a-dadosVeiculos';

module.exports.callback = ($) => {
    var loading = require('../../../components/Loading');
    var totalVeiculo = $('.data-total-veiculos').val();
    var totalFotos = $('.data-total-fotos').val();
    // tempo download de fotos + tempo upload fotos + tempo importação veículos
    var tempoTotalSegundos = (totalFotos * 300 + totalFotos * 100 + totalVeiculo * 250) / 1000;

    if (tempoTotalSegundos < 60) {
        tempoTotalSegundos = Math.round(tempoTotalSegundos) + ' Segundos';
    } else {
        tempoTotalSegundos = Math.round(tempoTotalSegundos / 60) + ' minutos';
    }
    console.log(tempoTotalSegundos);

    loading.addFeedbackTexts([
        'Importando ' + totalVeiculo + ' veículos ...',
        'Baixando ' + totalFotos + ' fotos...',
        'Tempo estimado ' + tempoTotalSegundos + '...',
        'Não feche seu navegador!',
        'Salvando...',
    ]);
    $('#form-importar').submit(function () {
        loading.open();
    });
};

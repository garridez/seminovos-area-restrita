module.exports = function () {
    var loading = require('components/Loading');
    var stepsUrl = $('div.anuncio-steps [data-url]');
    var totalSteps = stepsUrl.length;
    if ($('#idVeiculo').val() !== '') {
        loading.addFeedbackTexts([
            'Carregando dados do seu veículo...',
            'Carregando fotos...',
            'Carregando video...',
        ]);
    }
    loading.open(true);
    stepsUrl.each(function (i) {
        var ctx = $(this);
        $.get(ctx.data('url'), function (data) {
            ctx.html(data);
            if (--totalSteps === 0) {
                $('.anuncio-steps').trigger('steps-loaded');
                loading.close(true);
                setTimeout(function () {
                    loading.close(true);
                }, 200);
            }
        });
    });
};

module.exports.seletor = '.c-criar-anuncio.a-index';

module.exports.callback = ($) => {
    require('bootstrap/js/dist/collapse.js');
    var advancedAlerts = require('../../../components/AdvancedAlerts').default;
    let modalZeroKmExibido = false;
    $('.step-container')
        .on('steps-loaded', function () {
            $('.plano-box .input-radio-plano').change(function () {
                $('.btn-continuar').click();
            });
        })
        .on('step:pre-change:plano', function () {
            //location.hash = 'plano';
            $('.step-controls').hide();

            if (!modalZeroKmExibido && $('#form_Plano').data('zerokm') == '1') {
                advancedAlerts.warning({
                    title: $('<span class="text-primary">').html('Atenção!'),
                    text:
                        'Veículos 0km <small>(até 500km é considerado como zero)</small>' +
                        ' a cada 30 dias é necessário um novo pagamento<br>' +
                        'Para renovar, escolhar um plano e na sequência efetue o pagamento.',
                    time: false,
                });
                modalZeroKmExibido = true;
            }
        })
        .on('step:exit:plano', function () {
            $('.step-controls').show();
        });
};

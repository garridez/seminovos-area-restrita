

module.exports.seletor = '.c-painel.a-index';
module.exports.callback = ($) => {
    let datepicker = require('js-datepicker');
    $(".input").mask("00/00/0000");
    let picker = datepicker(".input.date-timer-picker", {
        customDays: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
        customMonths: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
        overlayButton: "Enviar",
        overlayPlaceholder: 'Escolha um ano 4 dig',
        formatter: (input, date, instance) => {
            const value = date.toLocaleDateString()
            input.value = value // => '1/1/2099'
        }
    });
    var Chart = require('chart.js');
    var ctx = $(".graficoAnuncios");

    var dadosPlanos = ctx.closest('div')
    var myChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Nitro', 'Turbo', 'Básico'],
            datasets: [{
                label: 'Planos',
                data: [dadosPlanos.data('nitro'),dadosPlanos.data('turbo'),dadosPlanos.data('basico')],
                backgroundColor: [
                    'rgba(167, 39, 18, 1)',
                    'rgba(237, 153, 2, 1)',
                    'rgba(241, 209, 22, 1)'
                ],
                // borderColor: [
                //     'rgba(167, 39, 18, 1)',
                //     'rgba(237, 153, 2, 1)',
                //     'rgba(241, 209, 22, 1)'
                // ],
                borderWidth: 1
            }]
        },
        options: {
            aspectRatio: 1,
            legend: {
                position: "right",
            }
        }
    });


    $.ajax({
        'type': 'GET',
        'dataType': 'json',
        'url': '/painel/contador-por-marca',
        success: function(contador){

            console.log(contador);

        }
    })

};
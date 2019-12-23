

module.exports.seletor = '.c-painel.a-detalhe-anuncio';
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
    function renderGraph(dataGraph = { labels: [], values: [], label }, target) {
        target.height(50).width(50);
        new Chart(target, {
            type: 'line',
            data: {
                labels: dataGraph.labels,
                datasets: [{
                    data: dataGraph.values,
                    fill: false,
                    borderColor: "rgb(75, 192, 192)",
                    lineTension: 0.1,
                    label: dataGraph.label,
                    pointHitRadius: 25
                }]
            },
            options: {
                maintainAspectRatio: false,
                legend: {
                    display: false,
                },
            }
        });
    }
    let dataGraph = {
        labels: [
            "10/12",
            "11/12",
            "12/12",
            "13/12",
            "14/12",
        ],
        values: [
            18,
            19,
            16,
            21,
            45,
        ],
        label: "Cliques no anúnico"
    }
    $(".grafCliques").length ? renderGraph(dataGraph, $(".grafCliques")) : false;
    $(".grafPropostas").length ? renderGraph(dataGraph, $(".grafPropostas")) : false;
    $(".grafCliquesTelefone").length ? renderGraph(dataGraph, $(".grafCliquesTelefone")) : false;
};
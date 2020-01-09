

module.exports.seletor = '.c-painel.a-detalhe-anuncio';
module.exports.callback = ($) => {
    /* let datepicker = require('js-datepicker');
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
    }); */
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
                    pointHitRadius: 1
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

    var labels = {
        'acesso': 'Cliques no anúncio',
        'contato': 'Cliques em ver telefone',
        'impressao': 'Impressões do veiculo'
    }
   
    Array('acesso', 'contato', 'impressao').map( tipo => {
        
        $.ajax({
            'url': location.href + '/grafico-contagem-diaria/'+tipo,
            'type': 'GET',
            'dataType': 'JSON',
            'success': function(retorno){
    
                if(!retorno.data) {
                    return false;
                }
    
                contador = Object.values(retorno.data);
    
                let labels = Array();
                let values = Array();
                contador.forEach(cnt => {
                    labels.push( new Date(cnt.data).toLocaleDateString());
                    values.push( parseInt(cnt.contador));
                })
                

                label = labels[tipo];
    
                let dataGraph = {
                    labels,
                    values,
                    label
                }
    
                $(".graf" + tipo).length ? renderGraph(dataGraph, $(".graf" + tipo)) : false;
    
            }
        })
        
    })
};
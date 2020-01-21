

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
    var graficos = [];
    function renderGraph(dataGraph = { labels: [], values: [], label }, target, tipo) {
        target.height(50).width(50);
        graficos[tipo] = new Chart(target, {
            type: 'line',
            data: null,
            options: {
                maintainAspectRatio: false,
                legend: {
                    display: false,
                },
            }
        });
    }

    var labelsTxt = {
        'acesso': 'Cliques no anúncio',
        'contato': 'Cliques em ver telefone',
        'impressao': 'Impressões do veiculo'
    }

    var cor = {
        'acesso': "rgb(0, 196, 0)",
        'contato': "rgb(196, 0, 0)",
        'impressao': "rgb(0, 0, 196)",
    }
   
    Array('acesso', 'contato', 'impressao').map( tipo => {
        $(".graf"+tipo).length ? renderGraph({}, $(".graf"+tipo), tipo) : false;
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
                

                label = labelsTxt[tipo];
    
                let dataGraph = {
                    labels,
                    values,
                    label
                }


                    graficos[tipo].data.labels = labels;
                    graficos[tipo].data.datasets.push({
                        
                        data: values,
                        fill: false,
                        borderColor: cor[tipo],
                        lineTension: 0.1,
                        label: label,
                        pointHitRadius: 1
                        
                    }) 
                    graficos[tipo].update();
                
    
            }
        })
        
    })
};
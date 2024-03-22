

module.exports.seletor = '.c-painel.a-index';
module.exports.callback = ($) => {
    require('sortable-tablesort');

    var marcaModelo = require('components/MarcaModelo');

    marcaModelo($('.form-tabela-fipe'));

    $('#filtar-meus-veiculos').keyup(function () {
        var values = $(this).val().toLowerCase().trim().replace(/\s+/g, ' ').split(' ').filter(Boolean);
        if (values.length === 0) {
            $('#table-meus-veiculos').find('tbody tr').show();
            return;
        }


        $('#table-meus-veiculos').find('tbody tr').each(function () {
            var $this = $(this);
            var dataList = [
                $this.find('.data-placa').text().toLowerCase().trim(),
                $this.find('.data-marca-modelo').text().toLowerCase().trim(),
            ];

            var countOccurrence = 0;

            for (var str of dataList) {
                for (var value of values) {
                    if (str.includes(value)) {
                        show = true;
                        countOccurrence++;
                    }
                }

            }

            if (values.length === countOccurrence) {
                $this.show();
            } else {
                $this.hide();
            }

            //console.log(dataList);
            //$(this).show();
        })
    });

    /*let datepicker = require('js-datepicker');
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
    */

    setTimeout(function () {
        $.ajax({
            'type': 'GET',
            'dataType': 'json',
            'url': '/painel/contador-por-marca',
            success: function (retorno) {
                if (!retorno.data) {
                    return false
                }
                contador = Object.values(retorno.data);

                contador.map(cnt => {

                    let div1 = $("<div></div>").addClass("py-1");
                    let div2 = $("<div></div>").addClass("d-flex justify-content-between align-items-center");
                    let img = $(`<img class="img-responsive" style="height:30px" src='/img/brands/${cnt.marca.toLowerCase()}.svg'>`);
                    let div3 = $("<div></div>").addClass('busca');
                    let span1 = $('<span></span>').addClass('num-busca').html(parseInt(cnt.acesso).toLocaleString('pt'));
                    let span2 = $('<span></span>').addClass('text-busca');

                    div3.append(span1).append(span2);
                    div2.append(img).append(div3);
                    div1.append(div2);

                    $('.list-marcas').append(div1);
                });

            }
        })
    }, 0)

    setTimeout(function () {
        $.ajax({
            'type': 'GET',
            'dataType': 'json',
            'url': '/painel/contador-por-modelo',
            success: function (retorno) {

                if (!retorno.data) {
                    return false
                }
                contador = Object.values(retorno.data);

                contador.map(cnt => {

                    let div1 = $("<div></div>").addClass("py-1");
                    let div2 = $("<div></div>").addClass("d-flex justify-content-between align-items-center");
                    let b = $("<b></b>").html(cnt.modelo);
                    let div3 = $("<div></div>").addClass('busca');
                    let span1 = $('<span></span>').addClass('num-busca').html(parseInt(cnt.acesso).toLocaleString('pt'));
                    let span2 = $('<span></span>').addClass('text-busca');

                    div3.append(span1).append(span2);
                    div2.append(b).append(div3);
                    div1.append(div2);

                    $('.list-modelos').append(div1);
                });

            }
        })
    }, 500)

    setTimeout(function () {
        $.ajax({
            'type': 'GET',
            'dataType': 'json',
            'url': '/painel/contador-por-categoria',
            success: function (retorno) {

                if (!retorno.data) {
                    return false
                }
                contador = Object.values(retorno.data);

                contador.map(cnt => {

                    let div1 = $("<div></div>").addClass("py-1");
                    let div2 = $("<div></div>").addClass("d-flex justify-content-between align-items-center");
                    let small = $('<small></small>').html(cnt.categoria);
                    let div3 = $("<div></div>").addClass('busca');
                    let span1 = $('<span></span>').addClass('num-busca').html(parseInt(cnt.acesso).toLocaleString('pt'));
                    let span2 = $('<span></span>').addClass('text-busca');

                    div3.append(span1).append(span2);
                    div2.append(small).append(div3);
                    div1.append(div2);

                    $('.list-categorias').append(div1);
                });

            }
        })
    }, 1000)


    var linhas = undefined;

    var ordenarPor = {

        'Ordenar por': _ => { },

        'Mais recentes': (linhas) => {

            linhas.sort(function (a, b) {

                a = $(a).find('.data-cadastro').html().trim();
                a = a.split('\/').reverse().join('-');
                a = new Date(a);

                b = $(b).find('.data-cadastro').html().trim();
                b = b.split('\/').reverse().join('-');
                b = new Date(b);

                if (a === b) {
                    return 0
                }

                return a > b ? -1 : 1;
            })

            $('.tableveiculos').find('tbody').html(linhas);
        },

        'Mais antigos': (linhas) => {

            linhas.sort(function (a, b) {

                a = $(a).find('.data-cadastro').html().trim();
                a = a.split('\/').reverse().join('-');
                a = new Date(a);

                b = $(b).find('.data-cadastro').html().trim();
                b = b.split('\/').reverse().join('-');
                b = new Date(b);

                if (a === b) {
                    return 0
                }

                return a > b ? 1 : -1;
            })

            $('.tableveiculos').find('tbody').html(linhas);
        },

        'Mais clicados': (linhas) => {

            linhas.sort(function (a, b) {

                a = $(a).find('.cliques').html().trim();
                a = parseInt(a) || 0;

                b = $(b).find('.cliques').html().trim();
                b = parseInt(b) || 0;

                return b - a;
            })

            $('.tableveiculos').find('tbody').html(linhas);
        },

        'Menos clicados': (linhas) => {

            linhas.sort(function (a, b) {

                a = $(a).find('.cliques').html().trim();
                a = parseInt(a) || 0;

                b = $(b).find('.cliques').html().trim();
                b = parseInt(b) || 0;

                return a - b;
            })

            $('.tableveiculos').find('tbody').html(linhas);
        },

        'Status': (linhas) => {
            $(".status-veiculo").closest('tr').fadeIn();
        },

        'Ativo': (linhas) => {
            $(".status-veiculo[data-status='1']").closest('tr').fadeOut();
            $(".status-veiculo[data-status='2']").closest('tr').fadeIn();
        },

        'Inativo': (linhas) => {
            $(".status-veiculo[data-status='1']").closest('tr').fadeIn();
            $(".status-veiculo[data-status='2']").closest('tr').fadeOut();
        }
    }

    $('.filtrar-veiculos').on('click', function () {

        if (typeof linhas === 'undefined') {
            linhas = $('.tableveiculos').find('tbody tr').clone();
        }

        var ordem = $('select[name="ordenacao"]').val();

        var status = $('select[name="status"]').val();

        ordenarPor[ordem](linhas);
        ordenarPor[status]();

    })


    $('.form-tabela-fipe').submit(function (e) {
        e.preventDefault();

        data = $(this).serializeArray();

        $.ajax({
            url: '/painel/tabela-fipe',
            type: 'POST',
            dataType: 'json',
            data,
            success: function (retorno) {

                var cards = [];

                retorno.data.map(fipe => {
                    let card = $("<div></div>").addClass("col-md-5 card bg-secondary m-1");
                    let titulo = $("<small></small>").addClass("text-center");
                    titulo.append($("<strong></strong>").html(fipe.versao));
                    let valor = $("<h5></h5>").addClass("preco-fipe text-center border-bottom-0")
                        .html("R$ " + (parseFloat(fipe.valor) || 0).toLocaleString())
                    card.append(titulo);
                    card.append(valor);
                    cards.push(card);
                })

                $(".preco-fipe").html(cards);
            },
            error: function () {

            }
        })
    })
};

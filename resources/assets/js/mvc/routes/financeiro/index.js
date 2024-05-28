module.exports.seletor = '.c-financeiro.a-index';
module.exports.callback = ($) => {
    require('bootstrap/js/dist/util.js');
    require('bootstrap/js/dist/index.js');
    require('bootstrap/js/dist/tab');
    require('bootstrap/js/dist/collapse');
    require('bootstrap/js/dist/dropdown');

    require('jquery-mask-plugin');
    require('jquery-validation');
    require('jquery-validation/dist/additional-methods');
    require('jquery-validation/dist/localization/messages_pt_BR');

    var HandleApiError = require('components/HandleApiError');

    var optional = { translation: { '?': { pattern: /[0-9]/, optional: true } } };
    var formCC = $('.pagamento-cc-form');
    $('.retorno-pix').hide();

    var advancedAlerts = require('components/AdvancedAlerts');

    formCC.find('[name="validade_cartao"]').mask('00/00');
    formCC.find('[name="cvc_cartao"]').mask('999?', optional);
    formCC.find('[name="numero_cartao"]').mask('9999 9999 9999 9??? ????', optional);

    $('form.pagamento-cc-form, form.pagamento-boleto-form, form.pagamento-pix-form').submit(
        function (e) {
            e.preventDefault();
            var data = $(this).serializeArray();
            var tempo_contrato = $('.tab-content')
            .find("input[name='tempo_contrato']:checked")
            .data('tempo_contrato');

            data.push({
                name: 'tempo_contrato',
                value: tempo_contrato,
            });

            var Loading = require('components/Loading');
            Loading.addFeedbackTexts(
            'Validando informações...',
            'Realizando pagamento ...'
                false,
            );

            Loading.open();
            $btnSubmit = $(this).find('button[type="submit"]');
            var dataRedirectPagamento = {};
            dataRedirectPagamento.urlAguardando = '/historico-pagamentos';

            var ajaxDefaultParams = {
                url: '/carro/checkout/processar',
                cache: false,
                data: data,
                type: 'POST',
                dataType: 'json',
                success: function (httpResponse) {
                    if (httpResponse.html) {
                        $('.retorno-boleto').html(httpResponse.html);
                        return;
                    }
                    if (
                        httpResponse.data &&
                        httpResponse.data.hasOwnProperty('qr_code') &&
                        httpResponse.data.qr_code
                    ) {
                        $('.qrcode-img').attr('src', httpResponse.data.img_qr_code);
                        $('.text-pix').html(httpResponse.data.qr_code);
                        $('.form-pix').hide();
                        $('.retorno-pix').show();
                        Loading.close();
                        return;
                    }
                    if (httpResponse.type === 15002) {
                        /**
                         * @todo implementar essa função
                         */
                        pagamentoEmAndamento();
                    }
                    if (!httpResponse.hasOwnProperty('status') || httpResponse.status != 200) {
                        HandleApiError(httpResponse);
                        return;
                    }

                    /**
                     * Caso seja necessário redirecionar o cliente para alguma tela de pagamento
                     * como PagSeguro ou se escolhido a opção 'débito' da Cielo
                     *
                     * @param  boolean httpResponse.data.redirect Flag que indica se é ou não para redirecionar
                     * @return void
                     */
                    if (
                        httpResponse.data &&
                        httpResponse.data.hasOwnProperty('redirect') &&
                        httpResponse.data.redirect
                    ) {
                    if(httpResponse.data.url.indexOf('data.galaxpay.com.br') === -1){
                            window.location = httpResponse.data.url;
                            return;
                    }

                        window.open(httpResponse.data.url, '_blank');
                        dataRedirectPagamento.url = httpResponse.data.url || '';
                    modalPagamentoBoleto(dataRedirectPagamento);
                    } else {
                        var title = 'Pagamento aprovado!';
                        var text = $(`  <div>
                                        <div>É nescessário aguardar a atualização do site, <h5 class="text-primary font-weight-bold">tempo estimado 30 minutos</h5></div>
                                    </div>
                                `);
                        var closeText = 'Li e concordo';
                        var time = 0;
                        advancedAlerts.success({
                            title,
                            text,
                            closeText,
                            time,
                        });

                        $('.nav-main-financeiro [data-target="#tab-finalizar"]').tab('show');
                    }
                    $btnSubmit.prop('disabled', true);
                    Loading.close();
                },
                error: function (e) {
                    HandleApiError(e.responseText);
                    Loading.close();
                },
            };
            var ajaxParams = $.extend(ajaxDefaultParams, ajaxParams || {});
            $.ajax(ajaxParams);
        },
    );

    function modalPagamentoBoleto(data) {
        var text = `
        <div class="w-100 text-center flex-wrap">
            <div>
                <h5>Caso o Boleto não tenha sido baixado automaticamente clique no botão abaixo</h5>
            </div>
            <div><small>O Boleto também será encaminhado para o seu email. 😃</small></div>
        </div>`;
        var downloadBtn = $(
            `<a href="${data.url}" target="_BLANK" download="boleto_pagamento.pdf" class="btn btn-primary"><i class="fa fa-download mr-3" aria-hidden="true"></i>Baixar Boleto</a>`,
        ).on('click', function (e) {
            setTimeout(function () {
                window.location = data.urlAguardando;
            }, 1000);
            });

        advancedAlerts
            .success({
                text: text,
                title: $('<span>').html(`<span class='text-primary'>Aguardando Pagamento </span>`),
                time: false,
                closeText: `download`,
            })
            .find('.modal-footer')
            .html(downloadBtn);
    }

    $('.nav-main-financeiro li a').on('shown.bs.tab', function (e) {
        var target = $(this).data('target').replace('#tab-', '');
        var state = {
            planos: {
                prev: false,
                next: true,
                finish: false,
            },
            pagamento: {
                prev: true,
                next: false,
                finish: false,
            },
            finalizar: {
                prev: false,
                next: false,
                finish: true,
            },
        }[target];

        $('.pager .next')[!state.next ? 'addClass' : 'removeClass']('hide');
        $('.pager .previous')[!state.prev ? 'addClass' : 'removeClass']('hide');
        $('.pager .finish')[!state.finish ? 'addClass' : 'removeClass']('hide');
    });

    $('.table-condensed').on('change', function () {
        var clickado = $('.tab-content').find("input[name='tempo_contrato']:checked").closest('tr');
        var resultado = $('#resultado');
        var pagamento = $('#tab-pagamento');

        var plano = $(clickado).find('#plano').html();
        var desconto = $(clickado).find('#desconto').html();
        var economia = $(clickado).find('#economia').html();
        var valor = parseFloat(
            $(clickado).find('#valor').html().replace('.', '').replace(',', '.').replace(' ', ''),
        );
        var valorFormatado = valor.toLocaleString('pt-BR', { minimumFractionDigits: 2 });

        resultado.find('#desconto').html(desconto);
        resultado.find('#economia').html(economia);
        resultado.find('#total').html(valorFormatado);

        pagamento.find('#plano').html(plano);
        pagamento.find('#desconto').html(desconto);
        pagamento.find('#economia').html(economia);
        pagamento.find('#valor').html(valorFormatado);
        optionsParcelas(valor, plano);
    });
    var tabsCallback = {
        planos: function () {
            if (!$('form.form-planos')[0].checkValidity()) {
                require('components/Alerts').warning('Escolha a periodicidade do seu plano');
                return false;
            }
            return true;
        },
        pagamento: function () {
            return true;
        },
        finalizar: function () {
            return true;
        },
    };

    $('#rootwizard').on('click', 'a', function (e) {
        var $this = $(this);
        var direction = $this.data('nav-dir');
        if (direction === 'finish') {
            return true;
        }

        var idTab = $('.tab-content-main > .tab-pane.active').attr('id');
        if (idTab) {
            idTab = idTab.replace('tab-', '');
        }
        e.preventDefault();
        if (!tabsCallback[idTab] || !direction) {
            return;
        }

        if (tabsCallback[idTab]()) {
            $('.nav-main-financeiro [data-target="#tab-' + idTab + '"]')
                .closest('li')[direction]()
                .find('a').tab('show');
        }
    });
};

var optionsParcelas = (valor, plano) => {
    var generateOption = function (i) {
        return $('<option>')
            .attr('value', i + 1)
            .text(i + 1 + 'x de R$ ' + (valor / (i + 1)).toFixed(2));
    };
    var parcelas = $('#parcelas');
    parcelas.html('');
    switch (plano) {
        case 'Plano Mensal':
            parcelas.append(generateOption(0));
            break;
        case 'Plano Trimestral':
            for (let i = 0; i < 3; i++) {
                parcelas.append(generateOption(i));
            }
            break;
        case 'Plano Semestral':
            for (let i = 0; i < 6; i++) {
                parcelas.append(generateOption(i));
            }
            break;
        case 'Plano Anual':
            for (let i = 0; i < 8; i++) {
                parcelas.append(generateOption(i));
            }
            break;
        case 'Plano Anual':
            for (let i = 0; i < 8; i++) {
                parcelas.append(generateOption(i));
            }
            break;

        default:
            break;
    }
};

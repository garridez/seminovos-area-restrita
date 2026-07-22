import 'bootstrap/js/dist/util.js';
import 'bootstrap/js/dist/index.js';
import 'bootstrap/js/dist/tab';
import 'bootstrap/js/dist/collapse';
import 'bootstrap/js/dist/dropdown';
import 'jquery-mask-plugin';
import 'jquery-validation';
import 'jquery-validation/dist/additional-methods';
import 'jquery-validation/dist/localization/messages_pt_BR';

import advancedAlerts from '../../../components/AdvancedAlerts';
import Alerts from '../../../components/Alerts';
import Confirms from '../../../components/Confirms';
import Loading from '../../../components/Loading';

export const seletor = '.c-financeiro.a-index';
export const callback = ($: JQueryStatic) => {
    type ErrorDetails = Record<string, string[]>;

    type ErrorResponse = {
        error?:
            | string
            | {
                  message?: string;
                  details?: ErrorDetails;
              };
        title?: string;
        detail?: string;
        message?: string;
    };

    // ------------------------------------------------------------------
    // Feedback de erros — renderiza um card amigável em .pagamento-feedback
    // ------------------------------------------------------------------
    const $feedback = $('.pagamento-feedback');

    const limparErro = () => {
        $feedback.empty();
    };

    const mostrarErro = (titulo: string, mensagemHtml: string) => {
        const html = `
            <div class="pagamento-erro" role="alert">
                <div class="pagamento-erro__icone">
                    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                </div>
                <div>
                    <p class="pagamento-erro__titulo">${titulo}</p>
                    <div class="pagamento-erro__mensagem">${mensagemHtml}</div>
                    <div class="pagamento-erro__acoes">
                        <button type="button" class="btn btn-outline-danger js-fechar-erro">
                            Tentar novamente
                        </button>
                    </div>
                </div>
            </div>
        `;

        $feedback.html(html);
        $feedback[0]?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    };

    $feedback.on('click', '.js-fechar-erro', limparErro);

    /**
     * Extrai uma mensagem legível de qualquer formato de erro retornado
     * (CelCash, serviço de pagamentos ou API).
     */
    const extrairMensagemErro = (payload: ErrorResponse | string | undefined | null): string => {
        const fallback =
            'Não conseguimos processar seu pagamento agora.<br>' +
            'Nenhum valor foi cobrado — confira os dados e tente novamente.';

        if (!payload) {
            return fallback;
        }

        if (typeof payload === 'string') {
            return payload;
        }

        if (typeof payload.error === 'string' && payload.error) {
            return payload.error;
        }

        if (payload.error && typeof payload.error === 'object') {
            let html = payload.error.message || 'Ocorreram erros de validação:';
            const details = payload.error.details || {};
            const itens: string[] = [];

            Object.values(details).forEach((messages) => {
                (messages || []).forEach((message) => itens.push(`<li>${message}</li>`));
            });

            if (itens.length) {
                html += `<ul>${itens.join('')}</ul>`;
            }
            return html;
        }

        if (payload.detail) {
            return payload.detail;
        }

        if (payload.message) {
            return payload.message;
        }

        return fallback;
    };

    // ------------------------------------------------------------------
    // Overlay de recorrência — cancelamento
    // ------------------------------------------------------------------
    $('.js-cancelar-recorrencia').on('click', function () {
        Confirms.warning({
            title: 'Cancelar pagamento recorrente?',
            text:
                '<div class="text-left">' +
                '<p>A cobrança automática no seu cartão <strong>para imediatamente</strong>.</p>' +
                '<p>Seu plano continua ativo até o fim do período já pago. Depois disso, ' +
                'será preciso pagar manualmente (PIX, cartão ou boleto) para manter ' +
                'seus anúncios no ar.</p>' +
                '<p class="mb-0">Deseja mesmo cancelar?</p>' +
                '</div>',
            confirmText: 'Sim, cancelar recorrência',
            negateText: 'Manter como está',
            confirmCallback: function () {
                $('.modal').modal('hide');
                Loading.addFeedbackTexts(['Falando com a operadora do cartão...', 'Cancelando a recorrência...']);
                Loading.open();

                $.ajax({
                    url: '/financeiro/cancelar-recorrencia',
                    type: 'POST',
                    dataType: 'json',
                })
                    .done(function (res: { status?: number; message?: string; error?: string }) {
                        Loading.close();

                        if (res && res.status === 200) {
                            advancedAlerts.success({
                                title: 'Recorrência cancelada',
                                text:
                                    '<p>Prontinho! A cobrança automática foi cancelada e nenhum novo ' +
                                    'valor será debitado do seu cartão.</p>' +
                                    '<p class="mb-0">Seu plano segue ativo até o fim do período já pago.</p>',
                                time: 8000,
                                closeCallback: function () {
                                    document.location.reload();
                                },
                            });
                            return;
                        }

                        advancedAlerts.error({
                            title: 'Não foi possível cancelar',
                            text: extrairMensagemErro(res),
                            time: 10_000,
                        });
                    })
                    .fail(function (xhr) {
                        Loading.close();
                        advancedAlerts.error({
                            title: 'Não foi possível cancelar',
                            text: extrairMensagemErro(xhr.responseJSON),
                            time: 10_000,
                        });
                    });
            },
        });
    });

    // ------------------------------------------------------------------
    // Máscaras + detecção de bandeira do cartão
    // ------------------------------------------------------------------
    const optional = { translation: { '?': { pattern: /[0-9]/, optional: true } } };
    const formCC = $('.pagamento-cc-form');
    $('.retorno-pix').hide();

    formCC.find('[name="validade_cartao"]').mask('00/00');
    formCC.find('[name="cvc_cartao"]').mask('999?', optional);
    formCC.find('[name="numero_cartao"]').mask('9999 9999 9999 9??? ????', optional);

    const detectarBandeira = (numero: string): string | null => {
        const d = numero.replace(/\D/g, '');
        if (!d.length) return null;

        const p2 = parseInt(d.substring(0, 2), 10);
        const p4 = parseInt(d.substring(0, 4), 10);

        if (/^4/.test(d)) return 'visa';
        if ((p2 >= 51 && p2 <= 55) || (p4 >= 2221 && p4 <= 2720)) return 'mastercard';
        if (p2 === 34 || p2 === 37) return 'amex';
        return null;
    };

    formCC.find('[name="numero_cartao"]').on('input', function () {
        const bandeira = detectarBandeira(String($(this).val() || ''));
        const $bandeiras = $('.cc-bandeiras');

        $bandeiras.toggleClass('has-match', !!bandeira);
        $bandeiras.find('.brand').removeClass('brand-match');
        if (bandeira) {
            $bandeiras.find(`.brand[data-brand="${bandeira}"]`).addClass('brand-match');
        }
    });

    // Link "Pague com PIX" dentro do card de boleto
    $('.pagamento-boleto-form').on('click', '.js-ir-para-pix', function (e) {
        e.preventDefault();
        $('[data-target="#pix_tab"]').tab('show');
    });

    // ------------------------------------------------------------------
    // Copia e cola do PIX
    // ------------------------------------------------------------------
    $('.retorno-pix').on('click', '#copy', function () {
        const codigo = String($('.text-pix').val() || '');
        const $btn = $(this);

        const copiado = () => {
            $btn.html('<i class="fa fa-check mr-1" aria-hidden="true"></i> Copiado!');
            setTimeout(() => {
                $btn.html('<i class="fa fa-copy mr-1" aria-hidden="true"></i> Copiar');
            }, 3000);
        };

        if (navigator.clipboard?.writeText) {
            navigator.clipboard.writeText(codigo).then(copiado);
        } else {
            const input = $('.text-pix')[0] as HTMLInputElement;
            input.select();
            document.execCommand('copy');
            copiado();
        }
    });

    // ------------------------------------------------------------------
    // Envio do pagamento (PIX / boleto / cartão)
    // ------------------------------------------------------------------
    $('form.pagamento-cc-form, form.pagamento-boleto-form, form.pagamento-pix-form').on(
        'submit',
        function (e) {
            e.preventDefault();
            limparErro();

            const data = $(this).serializeArray();
            const tempo_contrato = $('.tab-content')
                .find('input[name="tempo_contrato"]:checked')
                .data('tempo_contrato');

            data.push({
                name: 'tempo_contrato',
                value: tempo_contrato,
            });

            //FIELDS DATA ONLY
            const colorDepth = screen.colorDepth;
            const type = getDeviceType();
            const javaEnabled = false;
            const language = navigator.language;
            const screenHeight = screen.height;
            const screenWidth = screen.width;
            const timezoneOffset = getTimeZoneOffset();
            const userAgent = navigator.userAgent;

            data.push({ name: 'colorDepth', value: String(colorDepth) });
            data.push({ name: 'type', value: type });
            data.push({ name: 'javaEnabled', value: String(javaEnabled) });
            data.push({ name: 'language', value: language });
            data.push({ name: 'screenHeight', value: String(screenHeight) });
            data.push({ name: 'screenWidth', value: String(screenWidth) });
            data.push({ name: 'timezoneOffset', value: timezoneOffset });
            data.push({ name: 'userAgent', value: userAgent });

            Loading.addFeedbackTexts(['Validando informações...', 'Realizando pagamento ...']);

            Loading.open();
            const dataRedirectPagamento: ModalPagamentoBoletoParam = {
                url: '',
                urlAguardando: '/historico-pagamentos',
            };

            let checkout_endpoint = '';
            const metodo = data.find((x) => x.name === 'metodo')?.value;

            switch (metodo) {
                case 'pix':
                    checkout_endpoint = 'https://pagamentos.seminovos.com.br/pix/charge';
                    break;

                case 'card':
                    checkout_endpoint = 'https://pagamentos.seminovos.com.br/card/charge';
                    break;

                case 'boleto':
                    checkout_endpoint = 'https://pagamentos.seminovos.com.br/boleto/charge';
                    break;

                default:
                    checkout_endpoint = '/carro/checkout/processar';
                    break;
            }

            const ajaxDefaultParams: JQuery.AjaxSettings = {
                url: checkout_endpoint,
                cache: false,
                data: data,
                type: 'POST',
                dataType: 'json',
                success: function (httpResponse: any) {
                    Loading.close();

                    if (metodo == 'pix') {
                        if (httpResponse.status == 'ok') {
                            $('.qrcode-img').attr('src', httpResponse.pix.image || '');
                            $('.text-pix').val(httpResponse.pix.qrCode);
                            $('.form-pix').hide();
                            $('.retorno-pix').show();
                            $('.retorno-pix')[0]?.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center',
                            });
                        } else {
                            mostrarErro(
                                'Não conseguimos gerar seu PIX',
                                extrairMensagemErro(httpResponse),
                            );
                        }
                        return;
                    }

                    if (metodo == 'boleto') {
                        if (httpResponse.status == 'ok') {
                            dataRedirectPagamento.url = httpResponse.boleto.pdf;
                            modalPagamentoBoleto(dataRedirectPagamento);
                        } else {
                            mostrarErro(
                                'Não conseguimos gerar seu boleto',
                                extrairMensagemErro(httpResponse),
                            );
                        }
                        return;
                    }

                    if (metodo === 'card') {
                        if (httpResponse.status == 'captured') {
                            const ehRecorrente = String(tempo_contrato) === '1';
                            const title = 'Pagamento aprovado!';
                            const text = $(`<div>
                                            <h4 class="text-primary font-weight-bold">Tudo certo, seu plano foi renovado.</h4>
                                            ${
                                                ehRecorrente
                                                    ? '<p>A partir de agora a renovação é automática todo mês no seu cartão — sem boletos, sem preocupação. Você pode cancelar quando quiser aqui na Central de Pagamentos.</p>'
                                                    : ''
                                            }
                                            <h5 class="text-primary font-weight-bold">A ativação leva cerca de 30 minutos.</h5>
                                        </div>`);
                            const closeText = 'Li e concordo';
                            const time = 0;
                            advancedAlerts.success({
                                title,
                                text,
                                closeText,
                                time,
                            });

                            $('.nav-main-financeiro [data-target="#tab-finalizar"]').tab('show');
                        } else if (httpResponse.status == 'denied') {
                            mostrarErro(
                                'Pagamento não autorizado',
                                extrairMensagemErro(httpResponse) +
                                    '<br><small>Nenhum valor foi cobrado. Verifique os dados do ' +
                                    'cartão ou tente outro método de pagamento.</small>',
                            );
                        } else {
                            mostrarErro(
                                'Pagamento em processamento',
                                'Já existe uma transação em andamento. Aguarde alguns instantes ' +
                                    'antes de tentar novamente.',
                            );
                        }
                        return;
                    }
                },
                error: function (e) {
                    Loading.close();

                    const tituloPorMetodo: Record<string, string> = {
                        pix: 'Não conseguimos gerar seu PIX',
                        boleto: 'Não conseguimos gerar seu boleto',
                        card: 'Pagamento não autorizado',
                    };

                    mostrarErro(
                        tituloPorMetodo[String(metodo)] || 'Não conseguimos processar o pagamento',
                        extrairMensagemErro(e.responseJSON),
                    );
                },
            };
            $.ajax($.extend(ajaxDefaultParams, {}));
        },
    );

    /**
     * USED FOR DATA ONLY FIELD
     *
     * @returns {String} Retorna o tipo de dispositivo
     */
    function getDeviceType() {
        const userAgent = navigator.userAgent;

        if (/Mobi|Android|iPhone/i.test(userAgent)) {
            return 'Mobile';
        } else if (/iPad|Tablet/i.test(userAgent)) {
            return 'Tablet';
        } else {
            return 'Desktop';
        }
    }

    /**
     * USED FOR DATA ONLY FIELD
     *
     * @returns {String} Retorna o offset do fuso horário
     */
    function getTimeZoneOffset() {
        const offset = new Date().getTimezoneOffset();
        const offsetHours = Math.abs(offset / 60);
        const sign = offset < 0 ? '+' : '-';
        return `UTC${sign}${offsetHours}`;
    }
    type ModalPagamentoBoletoParam = {
        url: string;
        urlAguardando: string;
    };

    function modalPagamentoBoleto(data: ModalPagamentoBoletoParam) {
        const text = `
        <div class="w-100 text-center flex-wrap">
            <div>
                <h5>Caso o Boleto não tenha sido baixado automaticamente clique no botão abaixo</h5>
            </div>
            <div><small>O Boleto também será encaminhado para o seu email. 😃</small></div>
        </div>`;
        const downloadBtn = $(
            `<a href="${data.url}" target="_BLANK" ` +
                'download="boleto_pagamento.pdf" class="btn btn-primary">' +
                '<i class="fa fa-download mr-3" aria-hidden="true"></i>' +
                'Baixar Boleto' +
                '</a>',
        ).on('click', function () {
            setTimeout(function () {
                window.location.href = data.urlAguardando;
            }, 1000);
        });

        advancedAlerts
            .success({
                text: text,
                title: $('<span>').html('<span class="text-primary">Aguardando Pagamento </span>'),
                time: false,
                closeText: 'download',
            })
            .find('.modal-footer')
            .html(downloadBtn);
    }
    type TabsNames = 'planos' | 'pagamento' | 'finalizar';

    $('.nav-main-financeiro li a').on('shown.bs.tab', function () {
        if ($('#pix_tab').hasClass('active')) {
            //$('#pix-form').trigger('submit');
        }
        const target = $(this).data('target').replace('#tab-', '') as TabsNames;
        type StateType = {
            prev: boolean;
            next: boolean;
            finish: boolean;
        };
        const states: {
            [key in TabsNames]: StateType;
        } = {
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
        };
        const state: StateType = states[target];

        $('.pager .next')[!state.next ? 'addClass' : 'removeClass']('hide');
        $('.pager .previous')[!state.prev ? 'addClass' : 'removeClass']('hide');
        $('.pager .finish')[!state.finish ? 'addClass' : 'removeClass']('hide');
    });

    $('.table-condensed').on('change', function () {
        const clickado = $('.tab-content')
            .find('input[name="tempo_contrato"]:checked')
            .closest('tr');
        const resultado = $('#resultado');
        const pagamento = $('#tab-pagamento');

        const plano = $(clickado).find('#plano').html();
        const desconto = $(clickado).find('#desconto').html();
        const economia = $(clickado).find('#economia').html();
        const valor = parseFloat(
            $(clickado).find('#valor').html().replace('.', '').replace(',', '.').replace(' ', ''),
        );
        const valorFormatado = valor.toLocaleString('pt-BR', { minimumFractionDigits: 2 });

        resultado.find('#desconto').html(desconto);
        resultado.find('#economia').html(economia);
        resultado.find('#total').html(valorFormatado);

        pagamento.find('#plano').html(plano);
        pagamento.find('#desconto').html(desconto);
        pagamento.find('#economia').html(economia);
        pagamento.find('#valor').html(valorFormatado);
        optionsParcelas(valor, plano);
    });
    const tabsCallback = {
        planos: function () {
            if (!$<HTMLFormElement>('form.form-planos')[0].checkValidity()) {
                Alerts.warning('Escolha a periodicidade do seu plano');
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
        const $this = $(this);
        const direction: 'next' | 'prev' | 'finish' = $this.data('nav-dir');
        if (direction === 'finish') {
            return true;
        }

        let idTab = $('.tab-content-main > .tab-pane.active').attr('id') as TabsNames;
        if (idTab) {
            idTab = idTab.replace('tab-', '') as TabsNames;
        }
        e.preventDefault();
        if (!tabsCallback[idTab] || !direction) {
            return;
        }

        if (tabsCallback[idTab]()) {
            $('.nav-main-financeiro [data-target="#tab-' + idTab + '"]')
                .closest('li')
                [direction]()
                .find('a')
                .tab('show');
        }
    });
};

const optionsParcelas = (valor: number, plano: string) => {
    const generateOption = function (i: number) {
        return $('<option>')
            .attr('value', i + 1)
            .text(i + 1 + 'x de R$ ' + (valor / (i + 1)).toFixed(2));
    };
    const parcelas = $('#parcelas');
    const row_parcelas = $('#row_parcelas');
    parcelas.html('');
    const btn = $('#add_payment_form');
    const hintRecorrencia = $('#recorrencia-hint');
    btn.html('EFETUAR PAGAMENTO');
    hintRecorrencia.removeClass('show');
    switch (plano) {
        case 'Plano Mensal':
            parcelas.append(generateOption(0));
            btn.html('ATIVAR PAGAMENTO RECORRENTE');
            hintRecorrencia.addClass('show');
            row_parcelas.hide();
            break;
        case 'Plano Trimestral':
            for (let i = 0; i < 3; i++) {
                parcelas.append(generateOption(i));
            }
            row_parcelas.show();
            break;
        case 'Plano Semestral':
            for (let i = 0; i < 6; i++) {
                parcelas.append(generateOption(i));
            }
            row_parcelas.show();
            break;
        case 'Plano Anual':
            for (let i = 0; i < 12; i++) {
                parcelas.append(generateOption(i));
            }
            row_parcelas.show();
            break;
        default:
            break;
    }
};

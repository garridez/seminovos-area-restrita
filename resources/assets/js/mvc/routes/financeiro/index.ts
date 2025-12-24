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
import HandleApiError from '../../../components/HandleApiError';
import Loading from '../../../components/Loading';
import requestAlerts from '../criar-anuncio/checkout/request-alerts';

export const seletor = '.c-financeiro.a-index';
export const callback = ($: JQueryStatic) => {
    const pagamentoEmAndamento = function () {
        requestAlerts.erro('Existe uma transação em andamento! Aguarde');
    };
	
	type ErrorDetails = Record<string, string[]>;

	type ErrorResponse = {
		error?: {
			message?: string;
			details?: ErrorDetails;
		};
	};

	const buildErrorHtmlFromResponse = (responseJSON: ErrorResponse): string => {
		const error = responseJSON?.error;
		const details: ErrorDetails = error?.details || {};

		let html = `
		<div class="alert alert-danger d-flex align-items-start" role="alert">
			<i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
			<div>
				<p class="mb-1 fw-bold">
					${error?.message || 'Ocorreram erros de validação:'}
				</p>
				<ul class="error-list list-unstyled mb-0">
		`;

		Object.entries(details).forEach(([fieldName, messages]) => {
			messages.forEach((message: string) => {
				html += `
					<li class="d-flex align-items-start mb-1">
						<i class="bi bi-x-circle-fill me-2 mt-1"></i>
						<span>${message}</span>
					</li>
				`;
			});
		});

		html += `
				</ul>
			</div>
		</div>
		`;

		return html;
	};

	
    const optional = { translation: { '?': { pattern: /[0-9]/, optional: true } } };
    const formCC = $('.pagamento-cc-form');
    $('.retorno-pix').hide();

    formCC.find('[name="validade_cartao"]').mask('00/00');
    formCC.find('[name="cvc_cartao"]').mask('999?', optional);
    formCC.find('[name="numero_cartao"]').mask('9999 9999 9999 9??? ????', optional);

    $('form.pagamento-cc-form, form.pagamento-boleto-form, form.pagamento-pix-form').on(
        'submit',
        function (e) {
            e.preventDefault();
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
            const $btnSubmit = $(this).find('button[type="submit"]');
            const dataRedirectPagamento: ModalPagamentoBoletoParam = {
                url: '',
                urlAguardando: '/historico-pagamentos',
            };

            type ProcessarResponseType = {
                html?: string;
                type?: number;
                status: number;
                data?: {
                    qr_code?: string;
                    img_qr_code?: string;
                    redirect?: string;
                    url: string;
                };
            };
			
			let checkout_endpoint = '';
			const metodo = data.find(x => x.name === 'metodo')?.value;

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
					if(metodo == 'pix'){
						if(httpResponse.status == "ok"){
							$('.qrcode-img').attr('src', httpResponse.pix.image || '');
							$('.text-pix').html(httpResponse.pix.qrCode);
							$('.form-pix').hide();
							$('.retorno-pix').show();
							Loading.close();
						} else {
							if (httpResponse?.error) {
								requestAlerts.erro(httpResponse?.error);
								return;
							}
							
							HandleApiError(httpResponse);
							return;							
						}
                        return;
					}
					
					if(metodo == 'boleto'){
						if(httpResponse.status == "ok"){
							dataRedirectPagamento.url = httpResponse.boleto.pdf;
							modalPagamentoBoleto(dataRedirectPagamento);						
						} else {
							if (httpResponse?.error) {
								requestAlerts.erro(buildErrorHtmlFromResponse(httpResponse));
								return;
							}							
							
							HandleApiError(httpResponse);
							return;							
						}
                        return;
					}
					
					if(metodo === 'card') {
						if(httpResponse.status == "captured"){
							const title = 'Pagamento aprovado!';
							const text = $(`<div>
											<h4 class="text-primary font-weight-bold">Meio de pagamento cadastrado com sucesso!</h4>
											<h5 class="text-primary font-weight-bold">tempo estimado 30 minutos</h5>
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
						} else if(httpResponse.status == "denied"){
							if(httpResponse?.error){
								requestAlerts.erro(httpResponse?.error);
							} else {
								requestAlerts.erro(buildErrorHtmlFromResponse(httpResponse));
							}							
						} else {
							pagamentoEmAndamento();
						}
						return;					
					}
                },
                error: function (e) {
					console.log('erro');
					console.log(e);
					if (e.responseJSON?.error) {
						if(e.responseJSON?.error){
							requestAlerts.erro(e.responseJSON?.error);
						} else {
							requestAlerts.erro(buildErrorHtmlFromResponse(e.responseJSON));
						}
						Loading.close();
						return;
					}					
					
                    HandleApiError(e.responseJSON);
                    Loading.close();
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
	btn.html('EFETUAR PAGAMENTO');
    switch (plano) {
        case 'Plano Mensal':
            parcelas.append(generateOption(0));
			btn.html('ADICIONAR FORMA DE PAGAMENTO');
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

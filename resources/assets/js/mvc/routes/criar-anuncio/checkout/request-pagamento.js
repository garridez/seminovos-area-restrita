import $ from 'jquery';

import advancedAlerts from '../../../../components/AdvancedAlerts';
import DataLayerGTMPopulate from '../../../../helpers/DataLayerGTMPopulate';
import pagamentoEmAndamento from './pagamento-em-andamento';
import requestAlerts from './request-alerts';
/**
 *
 * @param array formData Dados adicionais na requisição
 * @param object ajaxParams Parametros para a função "ajax" do jQuery
 * @returns {undefined}
 */
export default function (formData, ajaxParams) {
    requestAlerts.processando();

    var data = $('#dados-basicos form').serializeArray();

    if (formData && Array.isArray(formData)) {
        data = data.concat(formData);
    }

    //FIELDS DATA ONLY
    const colorDepth = screen.colorDepth;
    const type = getDeviceType();
    const javaEnabled = false;
    const language = navigator.language;
    const screenHeight = screen.height;
    const screenWidth = screen.width;
    const timezoneOffset = getTimeZoneOffset();
    const userAgent = navigator.userAgent;

    data.push({ name: 'colorDepth', value: colorDepth });
    data.push({ name: 'type', value: type });
    data.push({ name: 'javaEnabled', value: javaEnabled });
    data.push({ name: 'language', value: language });
    data.push({ name: 'screenHeight', value: screenHeight });
    data.push({ name: 'screenWidth', value: screenWidth });
    data.push({ name: 'timezoneOffset', value: timezoneOffset });
    data.push({ name: 'userAgent', value: userAgent });

    var idVeiculo = $('#dados-basicos form').find('input[name="idVeiculo"]').val() || '';
    var dataRedirectPagamento = {
        urlAguardando: `/carro/novo/checkout/aguardando-pagamento?idVeiculo=${idVeiculo}`,
    };
	
	var checkout_endpoint = '/carro/checkout/processar';
	var metodo = null;
	
	if(formData[0].name == 'metodo'){
		switch(formData[0].value){
			case 'pix':
				checkout_endpoint = 'https://pagamentos.seminovos.com.br/pix/charge';
				metodo = 'pix';
				break;
			case 'card':
				checkout_endpoint = 'https://pagamentos.seminovos.com.br/card/charge';
				metodo = 'card';
				break;
			default:
				checkout_endpoint = '/carro/checkout/processar';
		}
	}
	
	console.log(formData);
	
	var tipo = 'carro';
	if(window.location.href.indexOf('caminhao') > -1)
		tipo = 'caminhao';
	else if(window.location.href.indexOf('moto') > -1)
		tipo = 'moto';
	
    var ajaxDefaultParams = {
        url: checkout_endpoint,
        cache: false,
        data: data,
        type: 'POST',
        dataType: 'json',
        success: function (httpResponse) {			
			if (metodo === 'pix') {
				if (
					httpResponse.status === "ok" &&
					httpResponse.pix &&
					httpResponse.pix.qrCode
				) {
					window.location = `/${tipo}/${httpResponse.idVeiculo}/checkout/pagamento-pix?idVeiculo=${httpResponse.idVeiculo}&code=${url_encode(httpResponse.pix.qrCode)}&idPagamento=${httpResponse.idPagamento}`;
				} else {
					requestAlerts.erro('Instabilidade ao gerar QRCode Pix. Por favor, tente novamente.');
				}
				return;
			}
			
			return;
			
            if (httpResponse.type === 15002) {
                /**
                 * @todo implementar essa função
                 */
                pagamentoEmAndamento();
                return;
            }
            if (!('status' in httpResponse) || httpResponse.status != 200) {
                requestAlerts.erro(httpResponse);
                return;
            }

            /**
             * Caso seja necessário redirecionar o cliente para alguma tela de pagamento
             * como PagSeguro ou se escolhido a opção 'débito' da Cielo
             *
             * @param  boolean httpResponse.data.redirect Flag que indica se é ou não para redirecionar
             * @return void
             */
            if ('redirect' in httpResponse.data && httpResponse.data.redirect) {
                var ctx = $('#dados-basicos form, .step-0, .step-1, .step-plano');
                DataLayerGTMPopulate(ctx, 'purchase', data);

                if (httpResponse.data.url.indexOf('data.galaxpay.com.br') === -1) {
                    window.location = httpResponse.data.url;
                    return;
                }

                window.open(httpResponse.data.url, '_blank');

                dataRedirectPagamento.url = httpResponse.data.url || '';
                modalPagamentoBoleto(dataRedirectPagamento);
            }
        },
        error: function (e) {
            requestAlerts.erro(e);
            console.log(e);
        },
    };
    var ajaxParamsMerged = $.extend(ajaxDefaultParams, ajaxParams || {});

    var text = `A Seminovos <b class='text-primary'>NÃO </b>faz contato por
        <b class='text-primary'>telefone </b> ou <b class='text-primary'>whatsapp </b>
        solicitando código de verificação de anúncio ou similar.<br><br>
        CUIDADO PARA NÃO CAIR EM GOLPES<br><br>
        Estamos à disposição para esclarecer dúvidas<br>`;
    advancedAlerts
        .error({
            text: text,
            title: $('<span>').html('<span class="text-primary">Alerta </span>importante'),
            time: false,
            img: $('<img src="/img/svg/ico_irregularidade.svg" class="modal-img">'),
            closeText: 'ESTOU CIENTE',
        })
        .on('hide.bs.modal', function () {
            $.ajax(ajaxParamsMerged);
        });

    function modalPagamentoBoleto(data) {
        var text = `
      <div class="w-100 text-center flex-wrap">
        <div>
          <h5>Caso o Boleto não tenha sido baixado automaticamente clique no botão abaixo</h5>
        </div>
        <div><small>O Boleto também será encaminhado para o seu email. 😃</small></div>
      </div>`;
        var downloadBtn = $(
            `<a href="${data.url}" target="_BLANK" download="boleto_pagamento.pdf" class="btn btn-primary">` +
                `<i class="fa fa-download mr-3" aria-hidden="true"></i>Baixar Boleto</a>`,
        ).on('click', function () {
            setTimeout(function () {
                window.location = data.urlAguardando;
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
}

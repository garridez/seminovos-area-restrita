/**
 *
 * @param array formData Dados adicionais na requisição
 * @param object ajaxParams Parametros para a função "ajax" do jQuery
 * @returns {undefined}
 */
module.exports = function (formData, ajaxParams) {
    var requestAlerts = require('./request-alerts');
    var pagamentoEmAndamento = require('./pagamento-em-andamento');
    requestAlerts.processando();

    var advancedAlerts = require('components/AdvancedAlerts');

    var DataLayerGTMPopulate = require('helpers/DataLayerGTMPopulate');

    var data = $('#dados-basicos form').serializeArray();

    if (formData && Array.isArray(formData)) {
        data = data.concat(formData);
    }

    var ajaxDefaultParams = {
        url: '/carro/checkout/processar',
        cache: false,
        data: data,
        type: 'POST',
        dataType: 'json',
        success: function (httpResponse) {
            if (httpResponse.type === 15002) {
                /**
                 * @todo implementar essa função
                 */
                pagamentoEmAndamento();
                return;
            }
            if (!httpResponse.hasOwnProperty('status') || httpResponse.status != 200) {
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
            if (httpResponse.data.hasOwnProperty('redirect') && httpResponse.data.redirect) {
                var ctx = $('#dados-basicos form, .step-0, .step-1, .step-plano');
                DataLayerGTMPopulate(ctx, 'purchase', data)
                window.location = httpResponse.data.url;
            }
        },
        error: function (e) {
            requestAlerts.erro(e);
            console.log(e);
        }
    };
    var ajaxParams = $.extend(ajaxDefaultParams, ajaxParams || {});

    var text = `A Seminovos <b class='text-primary'>NÃO </b>faz contato por
        <b class='text-primary'>telefone </b> ou <b class='text-primary'>whatsapp </b>
        solicitando código de verificação de anúncio ou similar.<br><br>
        CUIDADO PARA NÃO CAIR EM GOLPES<br><br>
        Estamos à disposição para esclarecer dúvidas<br>`;
    advancedAlerts.error({
        text: text,
        title: $("<span>").html(`<span class='text-primary'>Alerta </span>importante`),
        time: false,
        img: $('<img src="/img/svg/ico_irregularidade.svg" class="modal-img">'),
        closeText: "ESTOU CIENTE",
    }).on('hide.bs.modal', function () {
        $.ajax(ajaxParams);
    });
};

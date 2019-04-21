/**
 * 
 * @param array formData Dados adicionais na requisição
 * @param object ajaxParams Parametros para a função "ajax" do jQuery
 * @returns {undefined}
 */
module.exports = function (formData, ajaxParams) {
    var requestAlerts = require('./request-alerts');
    requestAlerts.processando();

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
                window.location = httpResponse.data.url;
            }
        },
        error: function (e) {
            requestAlerts.erro(e);
            console.log(e);
        }
    };
    var ajaxParams = $.extend(ajaxDefaultParams, ajaxParams || {});
    $.ajax(ajaxParams);
};
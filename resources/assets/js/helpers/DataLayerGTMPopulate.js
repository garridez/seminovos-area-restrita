import $ from 'jquery';
/**
 *
 * Função comum para popular o DataLayer Enhanced Ecommerce
 *
 */

module.exports = function (ctx, stepWhoCall = 'checkout_step_1', requestData = {}) {
    var { isDev } = require('../components/Env');
    if (isDev) {
        return;
    }
    window.dataLayer = window.dataLayer || [];
    var $ctx = $(ctx);
    var defaultVal = {
        name: '',
        id: '',
        price: '',
        brand: '',
        category: '',
        variant: '',
        quantity: 1,
    };
    var currentVal = $.extend({}, defaultVal);

    currentVal.brand = $ctx.find('select[name="idMarca"] :selected').html();
    currentVal.variant = $ctx.find('select[name="modeloCarro"] :selected').html();

    var tipoVei = parseInt($ctx.find('[name="tipoVeiculo"]').val());

    switch (tipoVei) {
        case 1:
            tipoVei = 'carro';
            break;
        case 2:
            tipoVei = 'caminhão';
            break;
        case 3:
            tipoVei = 'moto';
            break;
        default:
            tipoVei = 'carro';
            break;
    }

    currentVal.category = tipoVei;

    if (stepWhoCall == 'checkout_step_7') {
        currentVal.name = $ctx
            .find('[name="idPlano"]:checked')
            .closest('.plano-box')
            .find('h3')
            .html();
        currentVal.id = $ctx.find('[name="idPlano"]:checked').val();
        currentVal.price = parseFloat($ctx.find('[name="idPlano"]:checked').data('valor-plano'));
    }

    if (stepWhoCall == 'purchase') {
        var transaction_data = {
            payment_method: 'pagamento',
            id: '000000',
            affiliation: '',
            revenue: 0.0,
            tax: 0,
            shipping: 0,
            coupon: '',
        };

        if (requestData || false) {
            var pagamento = requestData.filter(function (val) {
                return val.name === 'metodo';
            })[0];

            transaction_data.payment_method = pagamento ? pagamento.value : null;
        }

        transaction_data.revenue = $ctx.find('[name="idPlano"]:checked').data('valor-plano');
        transaction_data.id = $ctx.find('[name="idVeiculo"]').val() + '-' + new Date().getTime();

        // console.log('SENDING TO GTM WITH TRANSACTION DATA');
        // console.log(transaction_data);
        // console.log(currentVal);

        return window.dataLayer.push({
            event: 'eec_dl_push',
            push_type: stepWhoCall,
            eec_data: {
                transaction_data: transaction_data,
                products: [currentVal],
            },
        });
    }

    // console.log('SENDING TO GTM ---' + stepWhoCall);
    // console.log(currentVal);

    return window.dataLayer.push({
        event: 'eec_dl_push',
        push_type: stepWhoCall,
        eec_data: {
            products: [currentVal],
        },
    });
};

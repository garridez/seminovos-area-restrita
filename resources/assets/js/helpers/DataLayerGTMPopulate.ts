import $ from 'jquery';

import isDev from '../components/Env';
/**
 *
 * Função comum para popular o DataLayer Enhanced Ecommerce
 *
 */
type DataLayerProduct = {
    name: string;
    id: string;
    price: number;
    brand: string;
    category: string;
    variant: string;
    quantity: number;
};
type DataLayerTransactionData = {
    payment_method: string | null;
    id: string;
    affiliation: string;
    revenue: number;
    tax: number;
    shipping: number;
    coupon: string;
};
type DataLayerEvent = {
    event: string;
    push_type: string;
    eec_data: {
        transaction_data?: DataLayerTransactionData;
        products: DataLayerProduct[];
    };
};
declare global {
    interface Window {
        dataLayer: DataLayerEvent[];
    }
}
type RequestDataType = { name: string; value: string | number };
export default function (
    ctx: HTMLElement | JQuery<HTMLElement>,
    stepWhoCall = 'checkout_step_1',
    requestData: RequestDataType[] = [],
) {
    if (isDev) {
        return;
    }
    window.dataLayer = window.dataLayer || [];
    const $ctx = $(ctx);
    const defaultVal: DataLayerProduct = {
        name: '',
        id: '',
        price: 0,
        brand: '',
        category: '',
        variant: '',
        quantity: 1,
    };
    const currentVal = $.extend({}, defaultVal);

    currentVal.brand = $ctx.find('select[name="idMarca"] :selected').html();
    currentVal.variant = $ctx.find('select[name="modeloCarro"] :selected').html();

    let tipoVei = 'carro';
    const tipoVeiSrc = parseInt($ctx.find<HTMLInputElement>('[name="tipoVeiculo"]').val() || '0');

    switch (tipoVeiSrc) {
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
        currentVal.id = String($ctx.find('[name="idPlano"]:checked').val() || '');
        currentVal.price = parseFloat($ctx.find('[name="idPlano"]:checked').data('valor-plano'));
    }

    if (stepWhoCall == 'purchase') {
        const transaction_data: DataLayerTransactionData = {
            payment_method: 'pagamento',
            id: '000000',
            affiliation: '',
            revenue: 0.0,
            tax: 0,
            shipping: 0,
            coupon: '',
        };

        if (requestData || false) {
            const pagamento = requestData.filter(function (val) {
                return val.name === 'metodo';
            })[0];

            transaction_data.payment_method = pagamento ? String(pagamento.value) : null;
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
}

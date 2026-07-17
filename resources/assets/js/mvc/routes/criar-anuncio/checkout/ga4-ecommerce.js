import $ from 'jquery';

/**
 * Dispara um evento de e-commerce do GA4 (begin_checkout, add_payment_info, ...)
 * via gtag, de forma segura: NUNCA lança nem bloqueia o fluxo do checkout.
 *
 * Lê o plano + certificado do resumo da compra no DOM. O `purchase` NÃO sai
 * daqui — ele é disparado server-side no webhook, na confirmação do pagamento.
 *
 * @param {string} eventName  ex.: 'begin_checkout', 'add_payment_info'
 * @param {object} [extra]     params extras (ex.: { payment_type: 'pix' })
 */
export default function dispararGA4Ecommerce(eventName, extra) {
    try {
        if (typeof window.gtag !== 'function') {
            return;
        }

        var idPlano = $('#idPlano').val() || $('#dados-basicos #idPlano').val() || '';

        // valor do plano (input hidden no resumo da compra)
        var $valor = $('[name="valor-plano"]').first();
        var valorPlano =
            parseFloat(
                String($valor.attr('data-valor-plano') || $valor.val() || '0').replace(',', '.'),
            ) || 0;

        // nome do plano (título do resumo)
        var nomePlano = ($('.resumo-compra .nome-plano').first().text() || '')
            .replace(/plano/i, '')
            .trim();
        if (!nomePlano) {
            nomePlano = 'Plano ' + idPlano;
        }

        var items = [
            {
                item_id: 'plano_' + idPlano,
                item_name: nomePlano,
                item_category: 'plano_anuncio',
                price: valorPlano,
                quantity: 1,
            },
        ];

        var value = valorPlano;

        // certificado documental (opcional)
        if ($('#servico-adicional-certificado').is(':checked')) {
            var valorCert = 39.9;
            items.push({
                item_id: 'certificado',
                item_name: 'Certificado Documental',
                item_category: 'servico_adicional',
                price: valorCert,
                quantity: 1,
            });
            value += valorCert;
        }

        var params = Object.assign(
            {
                send_to: 'G-MQD4MY64QS',
                currency: 'BRL',
                value: Number(value.toFixed(2)),
                items: items,
            },
            extra || {},
        );

        window.gtag('event', eventName, params);
    } catch (e) {
        // silencioso: analytics nunca quebra o checkout
    }
}

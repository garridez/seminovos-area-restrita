import 'jquery-mask-plugin';
import 'jquery-validation';
import 'jquery-validation/dist/additional-methods';
import 'jquery-validation/dist/localization/messages_pt_BR';

import $ from 'jquery';

import requestPagamento from './request-pagamento';
import { planoIncluiCertificado } from '../save-04-planos';

var CERTIFICADO_VALOR = 39.9;

function brl(v) {
    return 'R$ ' + Number(v || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

/**
 * Monta o select de parcelas conforme o maxParcelas do plano escolhido
 * (data-max-parcelas no radio do plano, vindo de adm > Planos de anúncio).
 * O total considera o certificado, para a parcela bater com a cobrança.
 * Antes o checkout não tinha esse campo e tudo ia como 1x.
 */
function montarParcelas() {
    var $campo = $('[data-parcelas-campo]');
    var $sel = $('#parcelas');
    if (!$campo.length || !$sel.length) {
        return;
    }

    var $radio = $('input[name="idPlano"]:checked').first();
    var max = parseInt($radio.data('maxParcelas') || $radio.attr('data-max-parcelas') || 1, 10) || 1;
    var valor = parseFloat(String($radio.data('valorPlano') || $radio.attr('data-valor-plano') || '0').replace(',', '.')) || 0;
    var acao = $('#acao').val() || '';

    // Troca de plano e certificado avulso são cobranças pontuais: à vista.
    if (acao === 'trocarPlano' || acao === 'addCertificado') {
        max = 1;
    }
    // Add-on só soma quando o plano NÃO inclui o certificado (senão cobraria em dobro).
    if ($('#servico-adicional-certificado').is(':checked') && !planoIncluiCertificado()) {
        valor += CERTIFICADO_VALOR;
    }

    if (max <= 1 || valor <= 0) {
        $sel.html('<option value="1">À vista</option>');
        $campo.prop('hidden', true).hide();
        return;
    }

    var anterior = $sel.val();
    var html = '';
    for (var n = 1; n <= max; n++) {
        var rotulo = n === 1
            ? '1x de ' + brl(valor) + ' (à vista)'
            : n + 'x de ' + brl(valor / n) + ' sem juros';
        html += '<option value="' + n + '">' + rotulo + '</option>';
    }
    $sel.html(html);
    if (anterior && parseInt(anterior, 10) <= max) {
        $sel.val(anterior);
    }
    $campo.prop('hidden', false).show();
}

export default function () {
    var optional = { translation: { '?': { pattern: /[0-9]/, optional: true } } };
    var formCC = $('.pagamento-cc-form');

    // Recalcula as parcelas quando o plano, o certificado ou a ação mudam.
    montarParcelas();
    $(document).on('change', 'input[name="idPlano"], #servico-adicional-certificado', montarParcelas);
    // No checkout, outro handler do mesmo evento define #acao (trocarPlano/addCertificado)
    // via .val(), que não dispara change. O setTimeout garante que lemos o valor final.
    $('.step-container').on('step:change:checkout', function () {
        setTimeout(montarParcelas, 0);
    });

    formCC.find('[name="validade_cartao"]').mask('00/00');
    formCC.find('[name="cvc_cartao"]').mask('999?', optional);
    formCC.find('[name="numero_cartao"]').mask('9999 9999 9999 9??? ????', optional);
    formCC.find('[name="cep"]').mask('99999-999', optional);
    formCC.validate({
        rules: {
            numero_cartao: {
                required: true,
                creditcard: true,
            },
            cep: {
                required: true,
            },
        },
        messages: {
            termos: 'É preciso ler e aceitar os termos para continuar',
        },
        submitHandler: function (form, event) {
            event.preventDefault();
            requestPagamento($(form).serializeArray());
            return false;
        },
    });
}

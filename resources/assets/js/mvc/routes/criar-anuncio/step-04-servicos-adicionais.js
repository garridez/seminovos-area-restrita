import $ from 'jquery';

import stopEvent from '../../../helpers/StopEvent';
import BtnContinuar from './helpers/BtnContinuar';
import { planoIncluiCertificado } from './save-04-planos';
export const seletor = '.c-criar-anuncio.a-index';
export const prepend = true;

export const callback = ($) => {
    $('.step-container').on('steps-loaded', init);
};

function init() {
    var $ctx = $('#form_servicos-adicionais');

    //var stepsContainer = $('.step-container.step-servicos-adicionais');

    $('.step-container').on('step:pre-change:servicos-adicionais', function () {
        if (window.fromCheckout) {
            BtnContinuar.get().removeClass('hide d-none');
            BtnContinuar.enable();
        }
    });

    $('.step-container').on('step:pre-change:servicos-adicionais', function (e) {
        if (
            $('#dados-basicos .placaVeiculo').val() == '' &&
            $('#dados-basicos #flagCriando').val() == 1
        ) {
            $('.btn-continuar').trigger('click');
            $('.step-container').stepPlugin('goTo', '.step-checkout');
            $('.step-container .step-servicos-adicionais').remove();
            return stopEvent(e);
        }

        // Plano com "Histórico veicular" já inclui o certificado: não faz sentido
        // vender o add-on (cobraria duas vezes). Marca no pedido e pula este passo.
        // Exceção: compra avulsa do certificado (addCertificado) para anúncio no ar.
        var compraAvulsa = location.hash && location.hash.indexOf('addCertificado') !== -1;
        if (planoIncluiCertificado() && !compraAvulsa) {
            $('input#servico-adicional-certificado').prop('checked', false);
            $('#dados-basicos .certificado').val(1);

            // Pula na direção em que a pessoa está indo. Não remove o passo (como o
            // caso sem placa faz): se ela voltar e trocar para um plano sem o
            // benefício, o add-on precisa voltar a aparecer.
            //
            // ATENÇÃO: há DOIS .step-container aninhados (o externo e o .step-veiculo).
            // Os índices têm que vir do plugin do container EXTERNO — ler ".active" no
            // DOM pegava um sub-passo interno, o cálculo dava "voltando" e o goTo caía
            // no passo atual, que o plugin trata como fora do intervalo e desativa
            // todos os passos (tela em branco).
            var $outer = $('.anuncio-steps.step-container').first();
            var atual = $outer.stepPlugin('getCurrentStepIndex');
            var alvo = $outer.stepPlugin('getStepIndex', '.step-servicos-adicionais');
            var voltando = atual !== false && alvo > -1 && alvo < atual;
            var destino = voltando ? '.step-plano' : '.step-checkout';

            // Nunca mandar para o passo atual (deixaria a tela em branco).
            if ($outer.stepPlugin('getStepIndex', destino) === atual) {
                return; // deixa o plugin seguir para o passo pedido
            }
            $outer.stepPlugin('goTo', destino);
            return stopEvent(e);
        }
    });

    $('input#servico-adicional-certificado').on('change', function () {
        var $this = $(this);

        var adicionar = $ctx.find('.btn-control-certificado .text-adicionar');
        var adicionado = $ctx.find('.btn-control-certificado .text-adicionado');

        adicionar.removeClass('hide');
        adicionado.removeClass('hide');

        if ($this.is(':checked')) {
            adicionar.hide();
            adicionado.show();
            $('.btn-continuar').trigger('click');
        } else {
            adicionado.hide();
            adicionar.show();
            $('.btn-continuar').trigger('click');
        }

        $('[data-adicionar-action]').prop('checked', $this.is(':checked'));
        $('#dados-basicos .certificado').val($this.is(':checked') ? 1 : '');
    });

    $('.step-container').on('step:change:servicos-adicionais', function () {
        if (
            location.hash &&
            location.hash.indexOf('addCertificado') !== -1 &&
            location.hash.indexOf('planoCem') !== -1
        ) {
            $('#acao').val('addCertificado');
        }
    });
}

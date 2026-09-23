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
            var $steps = $('.step-container').find('> [class*="step-"]');
            var atual = $steps.filter('.active').index();
            var alvo = $steps.filter('.step-servicos-adicionais').index();
            var voltando = atual > -1 && alvo > -1 && alvo < atual;
            $('.step-container').stepPlugin('goTo', voltando ? '.step-plano' : '.step-checkout');
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

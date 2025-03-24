import $ from 'jquery';

export const seletor = '.c-criar-anuncio.a-index';
export const prepend = true;

export const callback = ($) => {
    $('.step-container').on('steps-loaded', init);
};

function init() {
    var BtnContinuar = require('./helpers/BtnContinuar');
    var $ctx = $('#form_servicos-adicionais');
    var stopEvent = require('../../../helpers/StopEvent');
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
    });

    $('input#servico-adicional-certificado').change(function () {
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

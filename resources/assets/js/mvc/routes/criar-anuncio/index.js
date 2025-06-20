import '../../../components/StepPlugin';

import $ from 'jquery';

import Loading from '../../../components/Loading';
import BtnContinuar from './helpers/BtnContinuar';
import loadContentSteps from './load-content-steps';

/**
 * Este script faz a configuração inicial da página de anuncie
 */
export const seletor = '.c-criar-anuncio.a-index';
export const prepend = true; // Esse script precisa rodar primeiro
function stopEvent(e) {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();
    return false;
}
export const callback = ($) => {
    loadContentSteps();

    var stepsContainer = $('.step-container');
    var anuncioSteps = $('.anuncio-steps'); // Conjuto de steps principal

    // Troca o ícone ativo de acordo com o step ativo
    stepsContainer.on('step:change', setStepIconActive);
    stepsContainer.on('step:change', BtnContinuar.enable);
    stepsContainer.on('step:change', checkLastStep);
    stepsContainer.on('step:change', setHashState);
    anuncioSteps.on('steps-loaded', setStepIconActive);
    anuncioSteps.on('steps-loaded', function () {
        $('.step-controls').removeClass('d-none');
    });

    stepsContainer.stepPlugin().on('submit', 'form', function (e) {
        $(this).closest('.step-container').stepPlugin('next');
        return stopEvent(e);
    });
    $('.btn-voltar').on('click', function () {
        var stepContainer = $('.step-container [class*="step"].active').closest('.step-container');

        if (!$('.step-dados').length || $('.step-dados').is('.active')) {
            window.location.href = '/';
        } else {
            stepContainer.stepPlugin('prev');
        }
        checkLastStep();
    });

    function _alerta(type, text, title) {
        if (title === null || title === undefined) {
            title = '';
        }

        var close = $('<button class="btn btn" data-dismiss="modal">').html(
            '<span class="text-danger">Fechar</span> ',
        );

        var maisInfo = $(
            '<a class="btn btn-success" href="https://wa.me/5531971740697?text=Olá,%20Sou %20cliente%20da%20Seminovos%20e%20gostaria%20de%20mais%20informações!" target="blank">',
        ).html('<span class="text-close">Quero Saber Mais!</span>');

        var modal = $.jsBsModal({
            contents: {
                close: '',
                'modal-title': title,
                'modal-body': text,
                'modal-footer': [close, maisInfo],
            },
        }).on('hidden.bs.modal', function () {
            modal.modal('dispose').remove();
        });

        modal.find('.modal-content').addClass('alert alert-' + type);

        return modal;
    }

    $('body').on('click', '#remove-link-youtube', function () {
        // Remover a classe d-none e adicionar a classe d-flex ao div
        $('.preview-video').removeClass('d-none').addClass('d-flex');

        // Adicionar a classe d-none e remover o src do iframe
        var iframe = $('#videoWindow');
        iframe.addClass('d-none');
        iframe.attr('src', '');

        // Limpar o valor do input
        $('input[name="video"]').val('');
    });

    $('.btn-continuar').on('click', function () {
        // BtnContinuar.disable();
        // Valida no 1º passo se o modelo foi selecionado
        var modelo = $('[name="modeloCarro"]').val();
        if (modelo == 'Selecione o modelo') {
            alert('Selecione o modelo do veículo para continuar');
            return;
        }

        /*
         * se a aba step-preco ficar ativa valida a quantidade de caracteres da observacao
         * e desabilita o botão de continuar se necessario
         */
        setTimeout(function () {
            if (
                $('.step-preco').hasClass('active') &&
                $('textarea[name="observacoes"]').val().length > 650
            ) {
                BtnContinuar.disable();
            }
        }, 50);

        var inLastStep = $(this).data('in-last-step');
        var form = stepsContainer
            .find('[class*="step-"].active:visible:not(.step-container) form')
            .first();
        form.find('[type="submit"]').first().click();
        if (form[0] && !form[0].checkValidity()) {
            return;
        }

        if (inLastStep) {
            var redirect = function () {
                Loading.open();
                window.location.href = '/meus-veiculos/' + $('#idVeiculo').val();
            };
            // Se não tem ajax pendente, redireciona
            if ($.active === 0) {
                redirect();
                return;
            }
            // Espera todos os ajax terminarem com sucesso para então redirecionar
            $(document).ajaxComplete(function (_ev, jqXHR) {
                if ($.active - 1 !== 0) {
                    return;
                }
                if (jqXHR.status > 399) {
                    return;
                }
                if (jqXHR.responseJSON && jqXHR.responseJSON.status !== 200) {
                    return;
                }
                redirect();
            });
        }
    });

    stepsContainer
        .on('step:change:checkout step:change:finalizar', function () {
            BtnContinuar.disable();
            BtnContinuar.hide();
        })
        .on('step:change:servicos-adicionais', function () {
            BtnContinuar.enable();
            BtnContinuar.show();
        })
        .on('step:pre-exit:checkout step:pre-exit:finalizar', function () {
            BtnContinuar.enable();
        });

    $('.anuncio-steps').on('steps-loaded', function () {
        var hash = window.location.hash;
        if (!hash || hash === '#') {
            return;
        }
		
		$('.modal, .modal-backdrop').hide();
		
        var hashArr = hash.replace('#', '').split('&');
        hash = '.step-' + hashArr[0];

        $('.step-container').each(function () {
            var $this = $(this);
            if ($this.find(hash).length) {
                $this.stepPlugin('goTo', hash);
                $this.closest('[class*="step-"]:not(.anuncio-steps)').each(function () {
                    $(this).parent().closest('.step-container').stepPlugin('goTo', this);
                });
            } else {
                var lastStep = $this.stepPlugin('getSteps').last();
                $this.stepPlugin('goTo', lastStep);
            }
        });
    });
    /**
     *
     * Descomentar o autofill apenas em dev!
     */
    //(await import('./helpers/autofill')).default.init();
};
function allStepsInlast() {
    var inLastStep = true;

    $('.step-container').each(function () {
        if (!$(this).stepPlugin('inLastStep')) {
            inLastStep = false;
        }
    });
    return inLastStep;
}
function checkLastStep() {
    var btn = $('.step-controls .btn-continuar');
    var text = 'Continuar';
    var inLast = allStepsInlast();
    if (inLast) {
        text = 'Finalizar';
    } else {
        btn.addClass('btn-laranja').attr('disabled', false);
    }
    btn.text(text).attr('title', text).data('in-last-step', inLast);
}
function setStepIconActive() {
    var $anuncioSteps = $('.anuncio-steps');
    var stepsIcons = $('.steps-list li');

    // Remove os ícones que não tem steps
    stepsIcons = stepsIcons.filter(function () {
        var $this = $(this);
        var stepLabel = $this.data('step').split(',');
        var hasStep = false;
        $.each(stepLabel, function () {
            var seletor = '[data-step-label="' + this + '"]';
            if ($anuncioSteps.find(seletor).length) {
                hasStep = true;
            }
        });
        if (!hasStep) {
            $this.remove();
        }
        return hasStep;
    });
    $anuncioSteps.find('[class*="step-"].active').each(function () {
        var labelStep = $(this).data('step-label');
        if (labelStep) {
            stepsIcons
                .removeClass('active')
                .filter(function () {
                    var labels = $(this).data('step').split(',');
                    return labels.indexOf(labelStep) !== -1;
                })
                .addClass('active');
        }
    });
}

function setHashState(_e, params) {
    return;
    // eslint-disable-next-line no-unreachable
    var $element = params.stepElementDeep || params.stepElementTarget || $(this);
    var label = $element.data('step-label');

    if (!label) {
        return label;
    }
    location.hash = label;
}

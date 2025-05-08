import $ from 'jquery';

/**
 * Esse modulo é para gerenciar o estado dos alertas vindos do resultado do pagamento.
 * Normal: Remove todos os alertas
 * Processando: Mostra msg para aguardar a resposta do pagamento
 * Erro: Mostra o motivo do erro e um botão para tentar novamente
 */
export default {
    init: function () {
        this.ctx = $('.checkout-metodos-container');
        this.ctxBaseClass = this.ctx.attr('class');
        this.ctx.on('click', '.tentar-novamente', () => {
            this.normal();
        });

        // Reseta essa função para não ser executada novamente
        this.init = () => null;
    },
    ctx: undefined,
    ctxBaseClass: undefined,
    ctxClass: function (newClass) {
        this.ctx.removeClass().addClass(this.ctxBaseClass);
        if (newClass) {
            this.ctx.addClass(newClass);
        }
    },
    normal: function () {
        this.init();
        this.ctxClass();
        this.ctx.find('#accordion-payment').slideDown();
        this.ctx.find('.processando').slideUp();
        this.ctx.find('.error-output').slideUp();
    },
    processando: function () {
        this.init();
        this.ctxClass('status-processando');
        this.ctx.find('#accordion-payment').slideUp();
        this.ctx.find('.processando').slideDown();
        this.ctx.find('.error-output').slideUp();
    },
    erro: function (text) {
        this.init();
        this.ctxClass('status-erro');
        this.ctx.find('#accordion-payment').slideUp();
        this.ctx.find('.processando').slideUp();
        this.ctx.find('.error-output').slideDown();

        if (text && typeof text === 'object') {
            if (text.detail) {
                text = text.detail;
            } else if (text.title) {
                text = text.title;
            } else {
                text = false;
            }
        }
        if (!text) {
            text = 'Erro ao processar pagamento.<br>Tente novamente mais tarde';
        }

        this.ctx.find('.error-message').html(text);
    },
};

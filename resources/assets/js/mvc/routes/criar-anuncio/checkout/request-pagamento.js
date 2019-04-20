var pgtoDisplay = {
    ctx: $('.checkout-metodos-container'),
    ctxBaseClass: $('.checkout-metodos-container').attr('class'),
    ctxClass: function (newClass) {
        this.ctx.removeClass().addClass(this.ctxBaseClass);
        if (newClass) {
            this.ctx.addClass(newClass);
        }
    },
    normal: function () {
        console.log('pgtoDisplay: normal');
        return;
        this.ctxClass();
        this.ctx.find('.checkout-metodos').slideDown();
        this.ctx.find('.processando').slideUp();
        this.ctx.find('.error-output').slideUp();
    },
    processando: function () {
        console.log('pgtoDisplay: processando');
        return;
        this.ctxClass('status-processando');
        this.ctx.find('.checkout-metodos').slideUp();
        this.ctx.find('.processando').slideDown();
        this.ctx.find('.error-output').slideUp();

    },
    erro: function (text) {
        console.log('pgtoDisplay: erro');
        return;
        this.ctxClass('status-erro');
        this.ctx.find('.checkout-metodos').slideUp();
        this.ctx.find('.processando').slideUp();
        this.ctx.find('.error-output').slideDown();

        if (text && typeof text === "object") {
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
    }
};

module.exports = function (formData, ajaxParams) {
    pgtoDisplay.processando();

    var data = $('#dados-basicos form').serializeArray();


    if (formData && Array.isArray(formData)) {
        data = data.concat(formData);
    }


    var ajaxDefaultParams = {
        url: '/carro/checkout/processar',
        cache: false,
        data: data,
        type: 'POST',
//        dataType: 'json',
        success: function (text, httpResponse) {

            console.log(httpResponse);
            console.log(arguments);
            $('.output').html(text);
            return;
            if (typeof httpResponse === 'object') {
                for (var i in httpResponse) {
                    if (httpResponse.hasOwnProperty(i)) {
                        console.log(i + ':', httpResponse[i]);
                    }
                }
            }
            if (httpResponse.type === 15002) {
                pagamentoEmAndamento();
            }
            if (!httpResponse.hasOwnProperty('status') || httpResponse.status != 200) {
                pgtoDisplay.erro(httpResponse);
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
            pgtoDisplay.erro(e);
            console.log(e);
        }
    };
    var ajaxParams = $.extend(ajaxDefaultParams, ajaxParams || {});
    $.ajax(ajaxParams);
};
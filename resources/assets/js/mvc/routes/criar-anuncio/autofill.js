function randStr(length, type) {
    var result = '';

    var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    if (type === 'num') {
        characters = '0123456789';
    }
    var charactersLength = characters.length;
    for (var i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }
    return result;
}
module.exports = function (options) {
    var $ = require('jquery');

    var defaultOptions = {
        pararNoStep: false,
        placaAleatoria: true,
        placa: 'LZL5173',
        cartao: {
            validade_cartao: '12/25'
        }
    };
    options = $.extend({}, defaultOptions, options);

    if (options.placaAleatoria === true) {
        options.placa = randStr(3) + randStr(4, 'num');
    }


    function continuar() {
        setTimeout(function () {
            $('.btn-continuar').click();
        }, 500);
    }
    $('.anuncio-steps').on('steps-loaded', function () {
        setTimeout(populate, 1000);
    });
    // Metodo para agilizar o desenvolvimento
    function populate() {
        var form = $('#form_dadosVeiculo');

        form.find('[name="placa"]').val(options.placa);
        form.find('[name="idMarca"]').val('18');
        form.find('[name="modeloCarro"]').val('1964');
        form.find('[name="versao"]').val('3');
        form.find('[name="motor"]').val('3');
        form.find('[name="idValvula"]').val('2');
        form.find('[name="anoFabricacao"]').val('2015');
        form.find('[name="anoModelo"]').val('2015');
        form.find('[name="portas"]').val('3');
        form.find('[name="cor"]').val('Cinza');
        form.find('[name="combustivel"]').val('3');
        form.find('[name="kilometragem"]').val('180000');

        $('[name="valor"]').val('15000');
        $('[name="observacoes"]').val('Observação de teste');


        $('[name="troca"]').filter('[value="4"]').prop("checked", true);
        $('#radio-idPlano-3').click();

        setTimeout(function () {
            var ctx = $('.pagamento-cc-form');
            ctx.find('[name="numero_cartao"]').val('5442 5165 2311 6713');
            ctx.find('[name="nome_cartao"]').val('Felipe Rodrigues Amaral');
            ctx.find('[name="validade_cartao"]').val(options.cartao.validade_cartao);
            ctx.find('[name="cvc_cartao"]').val('654');
            $('#accordion-payment [name="termos"]').click();
            //ctx.find('.btn-submit-pagt').click();
        }, 500); // Pagamentos


        var stopContinuar = false;
        $('.step-container').on('step:exit', function (e) {
            if (stopContinuar) {
                return;
            }
            var sp = $('.step-container [class*="step"].active')
                    .closest('.step-container');
            var currentIndex = sp.stepPlugin('getCurrentStepIndex');
            var currentStep = sp.stepPlugin('getSteps', currentIndex);

            var classes = currentStep.attr("class");
            if (classes) {
                classes = classes.split(' ');

                if (options.pararNoStep && classes.indexOf(options.pararNoStep) !== -1) {
                    stopContinuar = true;
                    console.log('parado pelo step')
                    return;
                }
            }
            continuar();
        });
        setTimeout(function () {
            continuar();
        }, 500);

        return;
        var i = 500;

        function getTimeout() {
            setTimeout(function () {
                console.log();
            }, 500);
            i += 1000;
            return i;
        }

//        return;

        setTimeout(continuar, getTimeout()); // Dados
        setTimeout(continuar, getTimeout()); // Observações
        setTimeout(continuar, getTimeout()); // Fotos
        setTimeout(continuar, getTimeout()); // Videos
        setTimeout(continuar, getTimeout()); // Planos
        setTimeout(continuar, getTimeout()); // Pagamentos


        $('.step-container').on('step:change:checkout', function (e) {

            setTimeout(function () {
                var ctx = $('.pagamento-cc-form');
                //ctx.find('.btn-submit-pagt')[0].click();
            }, 100);
        });

    }
};
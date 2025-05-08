import $ from 'jquery';

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
function sleep(ms = 1000) {
    return new Promise((resolve) => setTimeout(resolve, ms));
}

const autofill = {
    init: function () {
        this._core({
            autofill: true,
            /**
             * Serve para ir passando simulando click e parar num step específico
             */
            pararNoStep: 'step-fotos',
            /**
             * Se true, é sempre gerado uma placa nova
             * Útil para não gerar conflito com placa existe
             * Mas cuidado pra não encher de cadastros diferentes
             */
            placaAleatoria: !false,
            //placaAleatoria: true,
            // Valor fixo de placa
            placa: 'LJL5173',
            cartao: {
                // Dados de validade do cartão
                validade_cartao: '12/25',
                // É possivel colocar uma data inválida para gerar error e ver as notificações
                //validade_cartao: '19/25',
            },
        });
    },
    _core: function (options) {
        if ($('#idVeiculo').val() !== '') {
            return;
        }
        var defaultOptions = {
            autofill: true,
            pararNoStep: false,
            placaAleatoria: true,
            placa: 'LZL5173',
            cartao: {
                validade_cartao: '12/25',
            },
        };
        options = $.extend({}, defaultOptions, options);

        if (options.placaAleatoria === true) {
            options.placa = randStr(3) + randStr(4, 'num');
        }

        function continuar() {
            setTimeout(function () {
                $('.btn-continuar').trigger('click');
            }, 10);
        }
        $('.anuncio-steps').on('steps-loaded', function () {
            if (options.autofill) {
                setTimeout(populate, 500);
            }
            setTimeout(avancar, 5000);
        });
        // Metodo para agilizar o desenvolvimento
        async function populate() {
            var form = $('#form_dadosVeiculo');

            form.find('[name="placa"]').val(options.placa);
            form.find('[name="idMarca"]').val('18').trigger('change');
            console.log('vai');
            await sleep(1000);
            console.log('vai');
            form.find('[name="modeloCarro"]').val('1964').trigger('change');
            await sleep(1000);

            form.find('[name="anoFabricacao"]').val('2015');
            form.find('[name="anoModelo"]').val('2015').trigger('change');
            await sleep(1000);
            form.find('[name="motor"]').val('3');

            await sleep(1000);
            form.find('[name="outraVersao"]').val('Outra versão');

            form.find('[name="versao"]').val('3');
            form.find('[name="idValvula"]').val('2');
            form.find('[name="portas"]').val('3');
            form.find('[name="cor"]').val('Cinza');
            form.find('[name="combustivel"]').val('3');
            form.find('[name="kilometragem"]').val('180000');
            form.find('[name="versao"]').val('-1');
            form.find('[name="idValvula"]').val('1');
            form.find('[name="checkboxacessorios[]"] [value="11"]').prop('selected', true);
            //            console.log('para')
            //            return;

            $('[name="valor"]').val('15000');
            $('[name="observacoes"]').val('Observação de teste');
            $('#form_maisInformacoesVeiculo [type="checkbox"][name="termo"]').trigger('click');

            $('[name="idTroca"]').filter('[value="1"]').prop('checked', true);
            $('#radio-idPlano-2').trigger('click');

            setTimeout(function () {
                var ctx = $('.pagamento-cc-form');
                ctx.find('[name="numero_cartao"]').val('5442 5165 2311 6713');
                ctx.find('[name="nome_cartao"]').val('Felipe Rodrigues Amaral');
                ctx.find('[name="validade_cartao"]').val(options.cartao.validade_cartao);
                ctx.find('[name="cvc_cartao"]').val('654');
                $('#accordion-payment [name="termos"]').trigger('click');
                //ctx.find('.btn-submit-pagt').trigger('click');
            }, 500); // Pagamentos
        }

        function avancar() {
            if (!options.pararNoStep) {
                return;
            }
            var stopContinuar = false;
            $('.step-container').on('step:exit', function () {
                if (stopContinuar) {
                    return;
                }
                var sp = $('.step-container [class*="step"].active').closest('.step-container');
                var currentIndex = sp.stepPlugin('getCurrentStepIndex');
                var currentStep = sp.stepPlugin('getSteps', currentIndex);

                var classes = currentStep.attr('class');
                if (classes) {
                    classes = classes.split(' ');

                    if (options.pararNoStep && classes.indexOf(options.pararNoStep) !== -1) {
                        stopContinuar = true;
                        console.log('parado pelo step');
                        return;
                    }
                }
                continuar();
            });
            continuar();
        }
    },
};

export default autofill;

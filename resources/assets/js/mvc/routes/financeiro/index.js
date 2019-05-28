
module.exports.seletor = '.c-financeiro.a-index';
module.exports.callback = ($) => {
    require('bootstrap/js/dist/util.js');
    require('bootstrap/js/dist/index.js');
    require('bootstrap/js/dist/tab');
    require('bootstrap/js/dist/collapse');
    require('bootstrap/js/dist/dropdown');

    require('jquery-mask-plugin');
    require('jquery-validation');
    require('jquery-validation/dist/additional-methods');
    require('jquery-validation/dist/localization/messages_pt_BR');


    var optional = {translation: {'?': {pattern: /[0-9]/, optional: true}}};
    var formCC = $('.pagamento-cc-form');

    formCC.find('[name="validade_cartao"]').mask("00/00");
    formCC.find('[name="cvc_cartao"]').mask("999?", optional);
    formCC.find('[name="numero_cartao"]')
            .mask("9999 9999 9999 9??? ????", optional);

    $(".table.table-condensed").on("change", function () {
        var clickado = $(".tab-content").find("input[name = 'options']:checked").parent().parent();
        let resultado = $("#resultado");
        let pagamento = $("#tab2");

        let plano = $(clickado).find("#plano").html();
        let desconto = $(clickado).find("#desconto").html();
        let economia = $(clickado).find("#economia").html();
        let valor = parseFloat($(clickado).find("#valor").html().replace('.', '').replace(',', '.').replace(' ', ''));
        let valorFormatado = valor.toLocaleString('pt-BR', {minimumFractionDigits: 2});

        resultado.find("#desconto").html(desconto);
        resultado.find("#economia").html(economia);
        resultado.find("#total").html(valorFormatado);

        pagamento.find("#plano").html(plano);
        pagamento.find("#desconto").html(desconto);
        pagamento.find("#economia").html(economia);
        pagamento.find("#valor").html(valorFormatado);
        optionsParcelas(valor, plano);
    });

};

var optionsParcelas = (valor, plano) => {
    var generateOption = function (i) {
        return $("<option>").attr("value", i + 1).text(i + 1 + "x de R$ " + ((valor / (i + 1)).toFixed(2)));
    };
    var parcelas = $("#parcelas");
    parcelas.html('');
    switch (plano) {
        case "Plano Trimestral":
            for (let i = 0; i < 3; i++) {
                parcelas.append(generateOption(i));
            }
            break;
        case "Plano Semestral":
            for (let i = 0; i < 6; i++) {
                parcelas.append(generateOption(i));
            }
            break;
        case "Plano Anual":
            for (let i = 0; i < 8; i++) {
                parcelas.append(generateOption(i));
            }
            break;

        default:
            break;
    }
};

module.exports.seletor = '.c-financeiro.a-index';
module.exports.callback = ($) => {
    require('bootstrap/js/dist/util.js');
    require('bootstrap/js/dist/index.js');
    require('bootstrap/js/dist/tab');

    require('jquery-mask-plugin');
    require('jquery-validation');
    require('jquery-validation/dist/additional-methods');
    require('jquery-validation/dist/localization/messages_pt_BR');


    var optional = { translation: { '?': { pattern: /[0-9]/, optional: true } } };
    var formCC = $('.pagamento-cc-form');

    formCC.find('[name="validade_cartao"]').mask("00/00");
    formCC.find('[name="cvc_cartao"]').mask("999?", optional);
    formCC.find('[name="numero_cartao"]')
        .mask("9999 9999 9999 9??? ????", optional);

    $(".tab-content").on("click", function () {
        var clickado = $(".tab-content").find("input[name = 'options']:checked").parent().parent();
        let resultado = $("#resultado");
        let pagamento = $("#tab2");

        let plano =    $(clickado).find("#plano")   .html();
        let desconto = $(clickado).find("#desconto").html();
        let economia = $(clickado).find("#economia").html();
        let valor =    $(clickado).find("#valor")   .html();

        resultado.find("#desconto").html(desconto);
        resultado.find("#economia").html(economia);
        resultado.find("#total").html(valor);

        pagamento.find("#plano").html(plano);
        pagamento.find("#desconto").html(desconto);
        pagamento.find("#economia").html(economia);
        pagamento.find("#valor").html(valor);


        console.log(plano + " " + desconto + " " + economia + " " + valor + " ");
    });
}

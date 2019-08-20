
module.exports.seletor = '.c-meus-dados.a-index';

module.exports.callback = ($) => {
    require('components/EstadoCidade')();
    var advancedAlerts = require('components/advancedAlerts');
    var resquestResponse = $("span[data-request-response]").data("request-response") || false;
    if (!resquestResponse) {
        return;
    }
    if (resquestResponse !== 200) {
        advancedAlerts.error({
            title: $("<span class='text-primary'>").html("Erro"),
            text: "Não conseguimos processar sua requisição, tente novamente mais tarde"
        });
        return;
    }
    advancedAlerts.success({
        text: $("<span>").html("Dados salvos com <b class='text-primary'>sucesso</b>"),
        title: $("<span class='text-primary'>").html("Sucesso")
    });
};
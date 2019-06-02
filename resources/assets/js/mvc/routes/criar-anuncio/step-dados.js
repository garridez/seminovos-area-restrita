module.exports.seletor = '.c-criar-anuncio.a-index';

module.exports.callback = ($) => {
    $('.step-container').on('steps-loaded', init);
};

function init() {
    var ctx = $('.step-dados');
    var anoFabricacao = ctx.find('[name="anoFabricacao"]');
    var anoFabricacaoOptions = anoFabricacao.find('option');
    var anoModelo = ctx.find('[name="anoModelo"]');
    var anoModeloOptions = anoModelo.find('option');
    var getValInt = function (element) {
        var val = parseInt($(element).val(), 10);
        if (Number.isNaN(val)) {
            return false;
        }
        return val;
    };
    anoFabricacao.change(function () {
        var anoF = getValInt(this);
        var anoModeloOptionsFiltred = anoModeloOptions;
        if (anoF !== false) {
            anoModeloOptionsFiltred = anoModeloOptions
                    .filter(function () {
                        var anoM = getValInt(this);
                        if (anoM === false) {
                            return true;
                        }
                        return anoF === anoM || (anoF === anoM - 1);
                    });
        }
        anoModelo.html('')
                .prepend(anoModeloOptionsFiltred)
                .val('');

    });
}
var $ = require('jquery');

function autoFill(_options) {
    var timeout = 3000;

    window.setTimeout(() => {
        console.log('autofill LOADED');
        makeAutoFill();
    }, timeout);
}

function makeAutoFill() {
    var fieldsType = {
        step1: {
            placa: { type: 'text', target: 'input[name="placa"]' },
            zeroKm: { type: 'checkbox', target: 'input[name="veiculo_zero_km"]' },
            marca: { type: 'select', target: 'select[name="idMarca"]' },
            modelo: { type: 'select', target: 'select[name="modeloCarro"]' },
            anoFab: { type: 'select', target: 'select[name="anoFabricacao"]' },
            anoMod: { type: 'select', target: 'select[name="anoModelo"]' },
            versao: { type: 'select', target: 'select[name="versao"]' },
            outraVer: { type: 'text', target: 'input[name="outraVersao"]' },
            cor: { type: 'select', target: 'select[name="cor"]' },
            portas: { type: 'select', target: 'select[name="portas"]' },
            combustivel: { type: 'select', target: 'select[name="combustivel"]' },
            leilao: { type: 'checkbox', target: 'select[name="flagLeilao"]' },
        },
    };

    var fieldsValue = {
        step1: {
            placa: makeRandomPlaca,
            zeroKm: checkRandom,
            marca: pickRandomOption,
            modelo: pickRandomOption,
            anoFab: pickRandomOption,
            anoMod: pickRandomOption,
            versao: pickRandomOption,
            outraVer: (field) => {
                $(field).val('teste123');
            },
            cor: pickRandomOption,
            portas: pickRandomOption,
            combustivel: pickRandomOption,
            leilao: checkRandom,
        },
    };

    Object.entries(fieldsType.step1).forEach((element, i) => {
        window.setTimeout(
            () => {
                console.log(element);
                fieldsValue.step1[element[0]](element[1].target);
            },
            (i + 1) * 2000,
        );
    });
}
function checkRandom(field) {
    var checked = Math.floor(Math.random() * 10) % 2 == 0;

    $(field).prop('checked', checked);
}

function pickRandomOption(field) {
    var options = $(field).find('option:not(:first-of-type)');
    var randomOption = Math.floor(Math.random() * options.length);

    var ret = options.eq(randomOption).prop('selected', true);
    $(field).change();
    return ret;
}

function makeRandomPlaca(field, model = 'AAA0000') {
    var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var placa = '';

    for (let index = 0; index < model.length; index++) {
        const char = model.charAt(index);

        if (char.match(/[A-Za-z]/g) != null) {
            placa += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        if (char.match(/[0-9]/g) != null) {
            placa += Math.floor(Math.random() * 10);
        }
    }

    return $(field).val(placa).blur();
}

module.exports = autoFill;

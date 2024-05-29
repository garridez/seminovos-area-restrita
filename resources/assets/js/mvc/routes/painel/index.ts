module.exports.seletor = '.c-painel.a-index';
module.exports.callback = ($: JQueryStatic) => {
    require('sortable-tablesort');

    require('../../../components/MarcaModelo')($('.form-tabela-fipe'));

    $('#filtar-meus-veiculos').on('keyup', function () {
        const values = ($(this).val() + '')
            .toLowerCase()
            .trim()
            .replace(/\s+/g, ' ')
            .split(' ')
            .filter(Boolean);
        if (values.length === 0) {
            $('#table-meus-veiculos').find('tbody tr').show();
            return;
        }

        $('#table-meus-veiculos')
            .find('tbody tr')
            .each(function () {
                const $this = $(this);
                const dataList = [
                    $this.find('.data-placa').text().toLowerCase().trim(),
                    $this.find('.data-marca-modelo').text().toLowerCase().trim(),
                    $this.find('.data-plano').text().toLowerCase().trim(),
                ];

                let countOccurrence = 0;

                for (const str of dataList) {
                    for (const value of values) {
                        if (str.includes(value)) {
                            countOccurrence++;
                        }
                    }
                }

                if (values.length === countOccurrence) {
                    $this.show();
                } else {
                    $this.hide();
                }
            });
    });
};

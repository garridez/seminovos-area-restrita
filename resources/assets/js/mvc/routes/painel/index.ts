import 'sortable-tablesort';

import MarcaModelo from '../../../components/MarcaModelo';

export const seletor = '.c-painel.a-index';
export const callback = ($: JQueryStatic) => {
    MarcaModelo($('.form-tabela-fipe'));

    $('body').on('click', '[data-table2excel]', function () {
        const date = new Date();
        // eslint-disable-next-line @typescript-eslint/ban-ts-comment
        // @ts-ignore
        const table2excel = new Table2Excel({
            defaultFileName:
                'estatisticas-seminovos-' + date.toLocaleDateString().replaceAll('/', '-'),
        });
        const seletor = $(this).data('table2excel');
        table2excel.export($(seletor).get(0));
    });

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

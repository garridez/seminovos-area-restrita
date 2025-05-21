import MarcaModelo from '../../../components/MarcaModelo';
type PlacaDisponivelResponseType = {
    [key: string]: any;
    status: number;
    placaDisponivel: boolean;
    historicoCarro?: {
        [key: string]: any;
        dados_veiculo?: {
            ano_fabricacao?: string;
            ano_modelo?: string;
            cidade?: string;
            estado?: string;
            cor?: string;
            combustivel?: string;
            especie?: string;
            modelo?: string;
            tipo_veiculo?: string;
            tipo_veiculo_id?: string;
            marca?: string;
            subsegmento?: string;
            carroceria?: string;
            cilindradas?: string;
            procedencia?: string;
        };
    };
};
export const seletor = '.c-repasse.a-anuncio';
export const callback = async ($: JQueryStatic) => {
    const $form = $('form.form-novo');
    const $inputs = {
        marca: $<HTMLSelectElement>('select[name="idMarca"]'),
        modelo: $<HTMLSelectElement>('select[name="modeloCarro"]'),
        cor: $<HTMLSelectElement>('select[name="cor"]'),
        ano_fabricacao: $<HTMLInputElement>('input[name="ano_fabricacao"]'),
        ano_modelo: $<HTMLInputElement>('input[name="ano_modelo"]'),
    };

    function showStep(seletor: string) {
        const $element = $form.find(seletor);
        console.log({
            visiable: $element.is(':visible'),
            dNonee: $element.hasClass('d-none'),
        });
        if (!$element.is(':visible') || $element.hasClass('d-none')) {
            $element.hide().removeClass('d-none').slideDown();
        }
    }

    $form.find('[name="tipoVeiculo"').on('change', function () {
        showStep('.step-2');
        MarcaModelo($form);
    });
    $('[name="placa"]').on('change', function () {
        const placa = String($(this).val());
        $.ajax({
            type: 'GET',
            url: '/carro/placa-disponivel/' + placa.toLowerCase(),
            dataType: 'json',
            success: function (response: PlacaDisponivelResponseType) {
                console.log({ response });
                console.log(JSON.stringify(response, null, 4));
                const historicoCarro = response.historicoCarro;
                if (!historicoCarro) {
                    showStep('.step-detalhes');
                    return;
                }

                const dados_veiculo = historicoCarro.dados_veiculo;
                if (!dados_veiculo) {
                    showStep('.step-detalhes');
                    return;
                }

                const setSelectValFromString = (
                    valor: string,
                    select: JQuery<HTMLSelectElement>,
                    disableOthers: boolean,
                ) => {
                    valor = valor.toLocaleLowerCase();
                    let matchRegex = -1;
                    const options = select.find('option');
                    options.each(function (k, v) {
                        const option = $(v);

                        const valorSelect = option.html().trim().toLowerCase();
                        //console.log({ marca: valorSelect, valor });
                        const regex = RegExp(valorSelect, 'i');
                        if (regex.test(valor) && valorSelect != '') {
                            if (matchRegex > -1) {
                                const previosOption = $(options[matchRegex]).html().trim();
                                matchRegex =
                                    previosOption.length > valorSelect.length ? matchRegex : k;
                            } else {
                                matchRegex = k;
                            }
                        }
                    });
                    if (matchRegex > -1) {
                        console.log(options[matchRegex]);
                        const option = $(options[matchRegex]);
                        option.prop('selected', true);

                        option.prop('selected', true);
                        select.trigger('change');
                        if (disableOthers) {
                            $('option:selected', select)
                                .prop('disabled', false)
                                .removeClass('hide');
                            $('option:not(:selected)', select)
                                .prop('disabled', true)
                                .addClass('hide');
                        }
                        return false;
                    }
                };

                if (dados_veiculo.marca) {
                    setSelectValFromString(dados_veiculo.marca, $inputs.marca, true);
                    if (dados_veiculo.modelo) {
                        setSelectValFromString(dados_veiculo.modelo, $inputs.modelo, false);
                        console.log($inputs.modelo);
                    }
                }
                if (dados_veiculo.cor) {
                    setSelectValFromString(dados_veiculo.cor, $inputs.cor, false);
                }
                if (dados_veiculo.ano_fabricacao) {
                    $inputs.ano_fabricacao.val(dados_veiculo.ano_fabricacao);
                }
                if (dados_veiculo.ano_modelo) {
                    $inputs.ano_modelo.val(dados_veiculo.ano_modelo);
                }
                showStep('.step-detalhes');
            },
        });
    });

    /* dev * /
    console.log('ookoko4');
    await sleep(100);
    $('#tipoCarro').prop('checked', true).trigger('change');
    await sleep();
    $('[name="placa"]').val('RNM6A16').trigger('change');
    /**/
};

function sleep(time: number = 500) {
    return new Promise(function (resolve) {
        setTimeout(() => {
            resolve(true);
        }, time);
    });
}

var $form;
var dataFilters;

metodos = {
    /**
     * Callbacks para tratar os valores
     */
    inputValFilter: {
        tipo: function (value) {
            return parseInt(value, 10);
        },
        marca: function (value) {
            return parseInt(value, 10);
        },
    },
    getTipo: function () {
        var tipo = this.getInputVal('tipoVeiculo').value;
        return dataFilters[tipo];
    },
    getMarcas: function () {
        return this.getTipo().marcas;
    },
    getModelos: function () {
        var marca = this.getInputVal('idMarca');
        var marcas = this.getMarcas();
        var marcaSelecionada = marcas.find( n => n.id == marca.value);
        return marcaSelecionada && marcaSelecionada.modelos ? marcaSelecionada.modelos : [];
    },
    getMotores: function () {
        var modelo = this.getInputVal('modelo');
        var modelos = this.getModelos();
        modelo = modelos.filter(function (e) {
            return e.id === modelo.value;
        })[0];
        return modelo ? modelo.motor : false;
    },
    getInputVal: function (name) {
        var input = $form.find('[name="' + name + '"]');
        if (input.length > 1) {
            input = input.filter(':checked');
        }
        var value = input.val();
        if (this.inputValFilter[name]) {
            value = this.inputValFilter[name](value);
        }
        if (input.length === 0) {
            return false;
        }

        return {
            value: value,
            input: input
        };
    },

    loadDataFilters: function (callback) {
        var date = new Date();
        date = date.getFullYear() + '-' + date.getMonth() + '-' + date.getDate();
        /**
         * @todo Colocar a versão do sistema junto com o date
         */
        $.getJSON('/filtros?' + date, function (data) {
            dataFilters = data;
            callback.call(this);
        });
    },
    campos: {
        marca: function () {
            var marcas = metodos.getMarcas();
            var marcaInput = metodos.getInputVal('idMarca');
            var self = this;

            metodos
                    .makeOptions(marcaInput.input, marcas, 'nome', 'id')
                    .prepend('<option selected value="">Selecione a marca</option>')
                    .val(marcaInput.value)
                    .unbind('change')
                    .change(function () {
                        self.modelos();
                    })
                    .change();
        },
        modelos: function () {
            var modelos = metodos.getModelos();
            var modeloInput = metodos.getInputVal('modeloCarro');
            if (modelos === false) {
                return;
            }
            metodos
                    .makeOptions(modeloInput.input, modelos, 'nome', 'id')
                    .prepend('<option selected value="">Selecione o modelo</option>');

            var value = modeloInput.value;
            if (value) {
                modeloInput.input
                        .find('option')
                        .filter(function () {
                            return value === $(this).val();
                        })
                        .parent()
                        .val(value);
            }
        },
    },
    autocomplete: function (params) {
        var self = this;
        $.each(params, function (i, v) {
            var inputVal = self.getInputVal(i);
            if (!inputVal) {
                return;
            }
            inputVal.input.val(v).change();
        });
    },
    makeOptions: function (select, options, keyHtml, keyValue) {
        keyValue = keyValue || keyHtml;
        var optionsString = '';
        var destaques = false;

        $.each(options, function (i, v) {
            var option = '<option value="' + v[keyValue] + '">' + v[keyHtml] + '</option>';
            if (v.destaque !== undefined && v.destaque !== false) {
                destaques = destaques || {};
                destaques[v.destaque] = option;
            }
            optionsString += option;
        });
        if (destaques !== false) {
            var destaquesHtml = '';
            $.each(destaques, function (i, html) {
                destaquesHtml += html;
            });
            optionsString = destaquesHtml + optionsString;
        }

        return select.html(optionsString);
    },
};
var filtro = {
    isInited: false,
    init: function (form, filterData) {
        if ((!form || form.length === 0) || this.isInited) {
            return;
        }
        this.isInited = true;
        $form = form;
        // Carrega os dados do filtro
        metodos.loadDataFilters(function () {
            metodos.campos.marca();

            if (filterData) {
                metodos.autocomplete(filterData);
            }
        });
        // Configura os input radio "uncheckebles"
        var resetUncheckableData = function ($form) {
            return $form.find('input[data-uncheckable]')
                    .each(function () {
                        $(this).data('checked', this.checked);
                    });
        };
        resetUncheckableData($form)
                .parent()
                .on('click', 'label', function (e) {
                    var $input = $(this).parent().find('input[data-uncheckable]');
                    var input = $input.get(0);
                    if ($input.data('checked')) {
                        input.checked = false;
                    } else {
                        input.checked = true;
                    }

                    resetUncheckableData($form);
                    e.preventDefault();
                    return false;
                });
    }
};

module.exports = filtro.init;
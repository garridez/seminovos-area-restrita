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
        return marcas[marca.value] ? marcas[marca.value].modelos : false;
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
            var marcaInput = $form.find('[name="idMarca"]');
            var self = this;
            metodos
                    .makeOptions(marcaInput, marcas, 'nome', 'id')
                    .prepend('<option selected value="">Selecione a marca</option>')
                    .unbind('change')
                    .change(function () {
                        self.modelos();
                    })
                    .change();
        },
        modelos: function () {
            var modelos = metodos.getModelos();
            var input = metodos.getInputVal('modeloCarro').input;
            input.html('<option selected value="">Selecione o modelo</option>');
            if (modelos !== false) {
                metodos
                        .makeOptions(input, modelos, 'nome', 'id')
                        .unbind('change')
                        /*.change(function () {
                         self.motores();
                         })*/
                        .change();
                input.prepend('<option selected value="">Selecione o modelo</option>');
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
            optionsString = destaquesHtml + '<option disabled>-</option>' + optionsString;
        }

        return select.html(optionsString);
    },
    money_format: function (num) {
        return num
                .toFixed(2)
                .replace('.', ',')
                .replace(/./g, function (c, i, a) {
                    return i && c !== "," && ((a.length - i) % 3 === 0) ? '.' + c : c;
                });
    },
    changeVeichle: function (num) {
        console.log('vamos la ', num);
    }
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
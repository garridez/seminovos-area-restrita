import $ from 'jquery';

export default function (form, filterData) {
	var $form;
	var dataFilters;
	var api;

	const metodos = {
		inputValFilter: {
			tipo: function (value) {
				return parseInt(value, 10);
			},
			marca: function (value) {
				return parseInt(value, 10);
			},
		},

		normalizeText: function (text) {
			return (text || '')
				.toString()
				.normalize('NFD')
				.replace(/[\u0300-\u036f]/g, '')
				.toLowerCase()
				.replace(/[^a-z0-9\s]/g, ' ')
				.replace(/\s+/g, ' ')
				.trim();
		},

		getInputVal: function (name) {
			var input = $form.find('[name="' + name + '"]');

			if (input.length === 0) {
				return false;
			}

			if (input.length > 1) {
				input = input.filter(':checked');
			}

			if (input.length === 0) {
				return false;
			}

			var value = input.val();

			if (this.inputValFilter[name]) {
				value = this.inputValFilter[name](value);
			}

			return {
				value: value,
				input: input,
			};
		},

		getTipo: function () {
			var tipo = this.getInputVal('tipoVeiculo');

			if (!tipo || !dataFilters || !dataFilters[tipo.value]) {
				return null;
			}

			return dataFilters[tipo.value];
		},

		getMarcas: function () {
			var tipo = this.getTipo();
			return tipo && Array.isArray(tipo.marcas) ? tipo.marcas : [];
		},

		getModelos: function () {
			var marca = this.getInputVal('idMarca');
			var marcas = this.getMarcas();

			if (!marca || !Array.isArray(marcas)) {
				return [];
			}

			var marcaSelecionada = marcas.find(function (n) {
				return String(n.id) === String(marca.value);
			});

			return marcaSelecionada && Array.isArray(marcaSelecionada.modelos)
				? marcaSelecionada.modelos
				: [];
		},

		getMotores: function () {
			var modelo = this.getInputVal('modeloCarro');
			var modelos = this.getModelos();

			if (!modelo || !Array.isArray(modelos)) {
				return false;
			}

			modelo = modelos.filter(function (e) {
				return String(e.id) === String(modelo.value);
			})[0];

			return modelo ? modelo.motor : false;
		},

		loadDataFilters: function (callback) {
			var date = new Date();
			var cacheKey =
				date.getFullYear() +
				'-' +
				String(date.getMonth() + 1).padStart(2, '0') +
				'-' +
				String(date.getDate()).padStart(2, '0');

			$.getJSON('/filtros?v=' + cacheKey, function (data) {
				dataFilters = data;
				callback.call(metodos);
			});
		},

		makeOptions: function (select, options, keyHtml, keyValue) {
			keyValue = keyValue || keyHtml;
			var optionsString = '';
			var destaques = false;

			$.each(options, function (_i, v) {
				var disabled = '';

				if (!/\w/.test(v[keyHtml])) {
					disabled = 'disabled';
				}

				var option =
					'<option ' +
					disabled +
					' value="' +
					v[keyValue] +
					'">' +
					v[keyHtml] +
					'</option>';

				if (v.destaque !== undefined && v.destaque !== false) {
					destaques = destaques || {};
					destaques[v.destaque] =
						'<option ' +
						disabled +
						' data="destaque" value="' +
						v[keyValue] +
						'">' +
						v[keyHtml] +
						'</option>';
				}

				optionsString += option;
			});

			if (destaques !== false) {
				var destaquesHtml = '';
				$.each(destaques, function (_i, html) {
					destaquesHtml += html;
				});
				optionsString = destaquesHtml + optionsString;
			}

			return select.html(optionsString);
		},

		findOptionByText: function (select, text) {
			var target = this.normalizeText(text);
			var found = null;

			if (!target) {
				return null;
			}

			select.find('option').each((_, option) => {
				var $option = $(option);
				var optionText = this.normalizeText($option.text());

				if (optionText === target) {
					found = $option;
					return false;
				}
			});

			return found;
		},

		findBestOptionByText: function (select, candidateTexts) {
			var candidates = (candidateTexts || [])
				.filter(Boolean)
				.map((v) => this.normalizeText(v))
				.filter(Boolean);

			if (!candidates.length) {
				return null;
			}

			var best = null;
			var bestScore = -9999;

			select.find('option').each((_, option) => {
				var $option = $(option);
				var optionTextRaw = $option.text();
				var optionText = this.normalizeText(optionTextRaw);

				if (!optionText) {
					return;
				}

				var score = 0;

				candidates.forEach((candidate) => {
					if (candidate === optionText) {
						score += 1000;
					}

					if (candidate.includes(optionText)) {
						score += 200 + optionText.length;
					}

					if (optionText.includes(candidate)) {
						score += 120 + candidate.length;
					}

					var optionTokens = optionText.split(' ').filter(Boolean);
					var candidateTokens = candidate.split(' ').filter(Boolean);

					optionTokens.forEach((token) => {
						if (token.length >= 3 && candidateTokens.includes(token)) {
							score += 20;
						}
					});
				});

				if (score > bestScore) {
					bestScore = score;
					best = $option;
				}
			});

			if (bestScore <= 0) {
				return null;
			}

			return best;
		},

		waitForOptions: function (select, minOptions, timeout) {
			minOptions = minOptions || 2;
			timeout = timeout || 3000;

			return new Promise((resolve, reject) => {
				var startedAt = Date.now();

				var timer = setInterval(function () {
					var total = select.find('option').length;

					if (total >= minOptions) {
						clearInterval(timer);
						resolve(true);
						return;
					}

					if (Date.now() - startedAt > timeout) {
						clearInterval(timer);
						reject(new Error('Timeout ao aguardar options do select'));
					}
				}, 80);
			});
		},

		campos: {
			marca: function () {
				var marcas = metodos.getMarcas();
				var marcaInput = metodos.getInputVal('idMarca');

				if (!marcaInput) {
					return;
				}

				metodos
					.makeOptions(marcaInput.input, marcas, 'nome', 'id')
					.prepend('<option selected value="">Selecione a marca</option>')
					.val(marcaInput.value)
					.off('change.marcaModelo')
					.on('change.marcaModelo', () => {
						this.modelos();
					})
					.trigger('change');
			},

			modelos: function () {
				var modelos = metodos.getModelos();
				var modeloInput = metodos.getInputVal('modeloCarro');

				if (!modeloInput) {
					return;
				}

				metodos
					.makeOptions(modeloInput.input, modelos, 'nome', 'id')
					.prepend($('<option value="">Selecione o modelo</option>').prop('selected', true));

				var value = modeloInput.value;
				if (value) {
					modeloInput.input.val(value);
				}
			},
		},

		autocomplete: function (params) {
			$.each(params, (i, v) => {
				var inputVal = this.getInputVal(i);
				if (!inputVal) {
					return;
				}
				inputVal.input.val(v).trigger('change');
			});
		},

		extrairCandidatosMarcaModelo: function (historico) {
			var dados = historico?.dados_veiculo || {};
			var fipe = historico?.fipe?.[0] || {};

			var marcaCandidates = [
				dados.marca,
				fipe.marca,
			].filter(Boolean);

			var modeloCandidates = [
				dados.modelo,
				fipe.modelo,
			].filter(Boolean);

			return {
				marcaCandidates: marcaCandidates,
				modeloCandidates: modeloCandidates,
			};
		},

		applyHistorico: async function (historico) {
			var parsed = this.extrairCandidatosMarcaModelo(historico);

			var marcaInput = this.getInputVal('idMarca');
			var modeloInput = this.getInputVal('modeloCarro');

			if (!marcaInput || !modeloInput) {
				return false;
			}

			var marcaOption = this.findBestOptionByText(
				marcaInput.input,
				parsed.marcaCandidates
			);

			if (!marcaOption) {
				return false;
			}

			marcaInput.input.val(marcaOption.val()).trigger('change');

			try {
				await this.waitForOptions(modeloInput.input, 2, 3000);
			} catch (_e) {}

			var modeloOption = this.findBestOptionByText(
				modeloInput.input,
				parsed.modeloCandidates
			);

			if (modeloOption) {
				modeloInput.input.val(modeloOption.val()).trigger('change');
			}

			return true;
		},
	};

	const moduleName = 'module-marca-modelo-init';
	if (!form || form.length === 0 || form.data(moduleName)) {
		return form ? form.data('marcaModeloApi') : null;
	}

	form.data(moduleName, true);
	$form = form;

	api = {
		applyHistorico: function (historico) {
			return metodos.applyHistorico(historico);
		},
		autocomplete: function (params) {
			return metodos.autocomplete(params);
		},
		refresh: function () {
			return metodos.campos.marca();
		},
		getDataFilters: function () {
			return dataFilters;
		},
	};

	form.data('marcaModeloApi', api);

	metodos.loadDataFilters(function () {
		metodos.campos.marca();

		if (filterData) {
			metodos.autocomplete(filterData);
		}
	});

	var resetUncheckableData = function ($form) {
		return $form.find('input[data-uncheckable]').each(function () {
			$(this).data('checked', this.checked);
		});
	};

	resetUncheckableData($form)
		.parent()
		.off('click.marcaModeloUncheckable', 'label')
		.on('click.marcaModeloUncheckable', 'label', function (e) {
			var $input = $(this).parent().find('input[data-uncheckable]');
			var input = $input.get(0);

			if (!input) {
				return false;
			}

			if ($input.data('checked')) {
				input.checked = false;
			} else {
				input.checked = true;
			}

			resetUncheckableData($form);
			e.preventDefault();
			return false;
		});

	return api;
}
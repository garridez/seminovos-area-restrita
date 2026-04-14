/**
 * Este script junta os dados do form, 'dados', 'preco' e 'mais-informacoes'
 */

function stopEvent(e) {
	e.preventDefault();
	e.stopPropagation();
	e.stopImmediatePropagation();
	return false;
}
import '../../../components/StepPlugin';

import advancedAlerts from '../../../components/AdvancedAlerts';
import HandleApiError from '../../../components/HandleApiError';
import Loading from '../../../components/Loading';
import marcaModelo from '../../../components/MarcaModelo';
import BtnContinuar from './helpers/BtnContinuar';

export const seletor = '.c-criar-anuncio.a-index';
export const callback = ($) => {
	var stepsContainer = $('.step-container.step-veiculo');
	var lastSavedData;
	var dataWithError;
	var formWithError;
	var marcaModeloApi = null;

	function normalizeText(text) {
		return (text || '')
			.toString()
			.normalize('NFD')
			.replace(/[\u0300-\u036f]/g, '')
			.toLowerCase()
			.replace(/[^a-z0-9\s]/g, ' ')
			.replace(/\s+/g, ' ')
			.trim();
	}

	function normalizeHistoricoResponse(response) {
		if (
			response.historicoCarro &&
			response.historicoCarro !== null &&
			response.historicoCarro.dados_veiculo &&
			Object.values(response.historicoCarro.dados_veiculo).length !== 0
		) {
			if (Array.isArray(response.historicoCarro.dados_veiculo)) {
				var dadosVeiculo = response.historicoCarro.dados_veiculo[0];
				response.historicoCarro.dados_veiculo = dadosVeiculo || false;
			}
		} else {
			response.historicoCarro = response.historicoCarro || {};
			response.historicoCarro.dados_veiculo = false;
		}

		return response;
	}

	function selecionarCor(historico) {
		if (!historico?.dados_veiculo?.cor) {
			return;
		}

		var corSelecionada = normalizeText(historico.dados_veiculo.cor).replace(/\b\w$/, '');
		var $select = $('select[name="cor"]');

		$select.find('option').each(function (_k, v) {
			var $option = $(v);
			var cor = normalizeText($option.val()).replace(/\b\w$/, '');

			if (cor && cor === corSelecionada) {
				$select.val($option.val()).trigger('change');
				return false;
			}
		});
	}

	function selecionarCombustivel(historico) {
		if (!historico?.dados_veiculo?.combustivel) {
			return;
		}

		var combustivelSelecionado = normalizeText(historico.dados_veiculo.combustivel);
		var $select = $('select[name="combustivel"]');

		$select.find('option').each(function (_k, v) {
			var $option = $(v);
			var combustivel = normalizeText($option.text());

			if (combustivel && combustivel === combustivelSelecionado) {
				$select.val($option.val()).trigger('change');
				return false;
			}
		});
	}

	async function preencherMarcaModeloPeloHistorico(historico) {
		if (!marcaModeloApi || !historico || !historico.dados_veiculo) {
			return;
		}

		try {
			await marcaModeloApi.applyHistorico(historico);
		} catch (_e) {}
	}

	async function preencherHistoricoCarro(response) {
		var historico = response?.historicoCarro;

		if (!historico || !historico.dados_veiculo) {
			return;
		}

		var dados = historico.dados_veiculo;
		var anoModelo = dados.ano_modelo;

		if (dados.ano_fabricacao && parseInt(dados.ano_fabricacao, 10)) {
			$('select[name="anoFabricacao"]').val(dados.ano_fabricacao).trigger('change');
		}

		if (dados.ano_modelo && parseInt(dados.ano_modelo, 10)) {
			if (anoModelo === '0' || anoModelo === null) {
				anoModelo = dados.ano_fabricacao;
			}
			$('select[name="anoModelo"]').val(anoModelo).trigger('change');
		}

		selecionarCor(historico);
		selecionarCombustivel(historico);

		var motoCilindradas = $('input[name="motoCilindradas"]');
		if (motoCilindradas.length && dados.cilindradas) {
			motoCilindradas.val(dados.cilindradas).trigger('input');
		}

		await preencherMarcaModeloPeloHistorico(historico);

		setTimeout(() => {
			$('select[name="anoModelo"]').trigger('change', [
				false,
				$('[name="caracteristicaVeiculo"]').val(),
			]);
		}, 0);
	}

	function setupPlacaBlur() {
		var placaAtual = ($('#placaVeiculo').val() || '').toUpperCase();
		var $placaInput = $('form[name="form_dadosVeiculo"]').find('input[name="placa"]');

		$placaInput
			.off('blur.historicoCarro')
			.on('blur.historicoCarro', async function () {
				var placaInput = $(this);
				var placa = (placaInput.val() || '').trim();

				placaInput.removeClass('is-invalid is-valid');

				if (!placa || placa.length < 7) {
					return;
				}

				if (placaAtual === placa.toUpperCase()) {
					return;
				}

				BtnContinuar.disable();

				$.ajax({
					type: 'GET',
					url: '/carro/placa-disponivel/' + placa.toLowerCase(),
					dataType: 'json',
					success: async function (response) {
						placaInput
							.parent()
							.removeClass('is-invalid is-valid')
							.addClass(response.placaDisponivel ? 'is-valid' : 'is-invalid');

						if (!response.placaDisponivel) {
							BtnContinuar.disable();
							advancedAlerts.error({
								title: 'Placa já cadastrada',
								text: 'Placa já cadastrada no sistema, confira a placa ou entre em cotato.',
								time: 10000,
							});
							return;
						}

						response = normalizeHistoricoResponse(response);
						await preencherHistoricoCarro(response);

						BtnContinuar.enable();
					},
					error: function () {
						BtnContinuar.enable();
					},
				});
			});
	}

	$('.anuncio-steps')
		.on('steps-loaded', function () {
			setTimeout(function () {
				let idStatus = $('input.idStatus').val();

				if (
					$("input[name='motoTrilha']").is(':checked') ||
					$("input[name='veiculo_zero_km']").is(':checked')
				) {
					$("input[name='placa']").removeAttr('required');
				} else {
					$("input[name='placa']").prop('required', 'true');
				}

				if (idStatus == 3 || idStatus == 6 || idStatus == 10) {
					$("input[name='placa']").prop('readonly', false).prop('disabled', false);
				}

				lastSavedData = $(
					'form',
					'#dados-basicos,.step-dados,.step-preco,.step-mais-informacoes'
				).serialize();
			}, 500);
		})
		.on('steps-loaded', function () {
			marcaModeloApi = marcaModelo($('#form_dadosVeiculo'));
			setupPlacaBlur();
		})
		.on('change', function () {
			if ($(this).stepPlugin('inLastStep')) {
				return;
			}
			BtnContinuar.enable();
		});

	var ajaxProcessing = false;

	stepsContainer.on('step:pre-change:mais-informacoes', function (_e) {
		var form = $('form', '#dados-basicos,.step-dados,.step-preco,.step-mais-informacoes');
		var dataSerialized = form.serialize();

		if (formWithError === true && dataSerialized === dataWithError) {
			BtnContinuar.disable();
		} else {
			BtnContinuar.enable();
		}
	});

	stepsContainer.on('step:pre-exit:mais-informacoes', function (e, extraParams) {
		var aceitaProposta = $('input[name="aceitaProposta"]').is(':checked');
		var aceitaLigacao = $('input[name="aceitaLigacao"]').is(':checked');
		var aceitaChat = $('input[name="aceitaChat"]').is(':checked');
		var tipoCadastro = $('input[name="tipoCadastro"]').val();
		var placa = $('form[name="form_dadosVeiculo"]').find('input[name="placa"]').val();

		if (!aceitaProposta && !aceitaLigacao && !aceitaChat && tipoCadastro == 2) {
			advancedAlerts.warning({
				text: 'Você precisa selecionar pelo menos um meio para contato',
				title: $('<span class="text-primary">').html('Atenção!'),
			});
			return stopEvent(e);
		}

		BtnContinuar.enable();

		if (ajaxProcessing) {
			return stopEvent(e);
		}

		ajaxProcessing = true;

		var formInfo = $('.step-mais-informacoes form');
		formInfo.find('[type="submit"]').first().click();

		if (!formInfo.get(0).checkValidity()) {
			ajaxProcessing = false;
			return stopEvent(e);
		}

		var form = $(
			'form',
			'#dados-basicos,.step-dados,.step-preco,.step-mais-informacoes,.step-opcionais'
		);
		var dataSerialized = form.serialize();

		if (formWithError && dataSerialized === dataWithError) {
			ajaxProcessing = false;
			if (extraParams.stepChangeTo && extraParams.stepIndex > extraParams.stepChangeTo) {
				return true;
			}
			return stopEvent(e);
		}

		if (!dataSerialized || dataSerialized === lastSavedData) {
			ajaxProcessing = false;
			return;
		}

		Loading.addFeedbackTexts([
			'Salvando dados do veículo...',
			'Salvando os acessórios...',
			'Salvando...',
		]);

		$.ajax({
			type: 'POST',
			url: '/carro/dados',
			data: dataSerialized,
			dataType: 'json',
			success: function (data) {
				ajaxProcessing = false;
				data = data.data;

				if (data) {
					var idVeiculo = data[0].idVeiculo;

					$('#dados-basicos .idVeiculo').val(idVeiculo);
					$('#dados-basicos .idAnuncioVeiculo').val(data[0].idAnuncio);
					$('#dados-basicos .idPlano').val(5);
					$('#dados-basicos .placaVeiculo').val(placa);

					$('#form_dadosVeiculo')
						.find('input:not([type="submit"]):not([type="button"]), select')
						.prop('disabled', true)
						.prop('readonly', true);

					var path = window.location.pathname.match(/^\/[a-z]+/).input + '/' + idVeiculo;
					window.history.pushState(null, null, path);
				}

				lastSavedData = form.serialize();
				stepsContainer.stepPlugin('next');
				formWithError = false;
			},
			error: function (e) {
				ajaxProcessing = false;
				formWithError = true;
				dataWithError = form.serialize();
				stepsContainer.stepPlugin('goTo', '.step-dados');

				if (e.responseJSON) {
					HandleApiError(e.responseJSON);
					if (e.responseJSON.detail) {
						$('#blockWords').html(e.responseJSON.detail.split('<br/>').at(-1));
					}
				} else {
					HandleApiError(false);
				}
			},
		});

		return stopEvent(e);
	});
};
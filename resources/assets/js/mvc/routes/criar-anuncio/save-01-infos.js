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

// ============================================================
// Normaliza texto: remove acentos, hífens, barras → minúsculo
// "Elétrico" → "eletrico", "I-Pace" → "i pace"
// "Eletrico / Fonte Externa" → "eletrico fonte externa"
// ============================================================
function normalizeStr(str) {
    if (!str) return '';
    return str
        .toLowerCase()
        .trim()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[\/\-]/g, ' ')
        .replace(/\s{2,}/g, ' ')
        .trim();
}

// ============================================================
// FIPE Parser — extrai dados estruturados da string de modelo FIPE
// ============================================================
function parseFipeModelo(fipeStr) {
    var remaining = fipeStr.trim();
    var result = {};

    // Câmbio
    var cambioMap = { 'aut': 'Automático', 'man': 'Manual', 'cvt': 'CVT' };
    remaining = remaining.replace(/\b(Aut|Man|CVT)\b\.?/i, function (m) {
        var key = m.replace('.', '').toLowerCase();
        result.cambio = cambioMap[key] || m;
        return '';
    });

    // Portas
    remaining = remaining.replace(/\b(\d)p\b/i, function (_m, n) {
        result.portas = parseInt(n, 10);
        return '';
    });

    // Combustível
    var combMap = {
        'dies': 'Diesel', 'diesel': 'Diesel', 'flex': 'Flex',
        'gas': 'Gasolina', 'gasolina': 'Gasolina',
        'elet': 'Elétrico', 'elétrico': 'Elétrico',
        'híb': 'Híbrido', 'gnv': 'GNV', 'alc': 'Álcool', 'álcool': 'Álcool',
    };
    remaining = remaining.replace(
        /\b(Dies|Diesel|Flex|Gas|Gasolina|Elet|Elétrico|Híb|Híbrido|GNV|Alc|Álcool)\b\.?/i,
        function (m) {
            var key = m.replace('.', '').toLowerCase();
            result.combustivel = combMap[key] || m;
            return '';
        },
    );

    // Turbo
    remaining = remaining.replace(/\bT\.?\s?I\.?\b/gi, function () { result.turbo = true; return ''; });
    remaining = remaining.replace(/\bTurbo\b/gi, function () { result.turbo = true; return ''; });

    // Tração
    remaining = remaining.replace(/\b(4x[24]|AWD|FWD|RWD|2WD)\b/i, function (m) {
        result.tracao = m.toUpperCase();
        return '';
    });

    // Motor
    remaining = remaining.replace(/\b(\d\.\d)\b/, function (m) { result.motor = m; return ''; });

    // Válvulas
    remaining = remaining.replace(/\b(\d{1,2})[Vv]\b/, function (_m, n) {
        result.valvulas = parseInt(n, 10);
        return '';
    });

    // Tokens técnicos
    remaining = remaining.replace(
        /\b(MPI|MPFI|DOHC|SOHC|VVT|VVTi|VTEC|TSI|TDI|HDI|CDI|CGI|GDI|EcoBoost|BlueHDi|JTD)\b/gi,
        function (m) {
            result.tecnologia = result.tecnologia || [];
            result.tecnologia.push(m.toUpperCase());
            return '';
        },
    );

    remaining = remaining.replace(/\s{2,}/g, ' ').replace(/^\s+|\s+$/g, '');
    result.remaining = remaining;
    return result;
}

// ============================================================
// Mapeamento de siglas Denatran/RENAVAM → nome comercial
// ============================================================
var marcaAliases = {
    'mmc': 'mitsubishi', 'gm': 'chevrolet', 'gm - chevrolet': 'chevrolet',
    'chevrolet - gm': 'chevrolet', 'vw': 'volkswagen', 'vw - volkswagen': 'volkswagen',
    'fiat': 'fiat', 'ford': 'ford', 'toyota': 'toyota',
    'hob': 'honda', 'honda': 'honda',
    'mbenz': 'mercedes-benz', 'mercedes-benz': 'mercedes-benz', 'm.benz': 'mercedes-benz',
    'bmw': 'bmw', 'nissan': 'nissan', 'jac': 'jac', 'jac motors': 'jac',
    'lr': 'land rover', 'land rover': 'land rover',
    'hyundai': 'hyundai', 'kia': 'kia', 'kia motors': 'kia',
    'peug': 'peugeot', 'peugeot': 'peugeot', 'ren': 'renault', 'renault': 'renault',
    'citr': 'citroën', 'citroën': 'citroën', 'citroen': 'citroën',
    'jeep': 'jeep', 'sub': 'subaru', 'subaru': 'subaru',
    'volvo': 'volvo', 'suzuki': 'suzuki', 'chery': 'chery',
    'jag': 'jaguar', 'jaguar': 'jaguar', 'porsche': 'porsche', 'audi': 'audi',
    'ram': 'ram', 'dodge': 'dodge', 'chrysler': 'chrysler', 'chev': 'chevrolet',
    'cad': 'cadillac', 'byd': 'byd', 'gwm': 'gwm',
    'caoa chery': 'caoa chery', 'troller': 'troller',
};

// ============================================================
// Mapeamento combustível Denatran → nome no select
// ============================================================
var denatranCombMap = {
    'eletrico fonte externa': 'Elétrico',
    'eletrico': 'Elétrico',
    'elet fonte ext': 'Elétrico',
    'gasolina alcool': 'Bi-Combustível',
    'alcool gasolina': 'Bi-Combustível',
    'gasolina etanol': 'Bi-Combustível',
    'gas metano': 'Kit Gás',
    'gas natural veicular': 'Kit Gás',
    'gnv': 'Kit Gás',
    'gasolina eletrico': 'Híbrido',
    'eletrico gasolina': 'Híbrido',
    'hibrido': 'Híbrido',
};

// ============================================================
// Helper: tenta setar um <select> por match normalizado
// ============================================================
function setSelectByMatch($, selectName, targetValue, form) {
    var container = form || $(document);
    var select = container.find('select[name="' + selectName + '"]');
    if (!select.length || !targetValue) return false;

    var target = normalizeStr(targetValue);
    var found = false;

    // Match exato normalizado
    select.find('option').each(function () {
        var option = $(this);
        var optVal = normalizeStr(option.text());
        if (optVal === target) {
            option.prop('selected', true);
            found = true;
            return false;
        }
    });
    if (found) return true;

    // Match parcial normalizado
    select.find('option').each(function () {
        var option = $(this);
        var optVal = normalizeStr(option.text());
        if (optVal && target && (optVal.indexOf(target) > -1 || target.indexOf(optVal) > -1)) {
            option.prop('selected', true);
            found = true;
            return false;
        }
    });
    return found;
}

// ============================================================
// Limpar campos preenchidos por placa anterior
// ============================================================
function resetFormFields($) {
    $('select[name="idMarca"] option').prop('disabled', false).removeClass('hide');
    $('select[name="idMarca"]').val('');
    $('select[name="modeloCarro"]').html('<option value="">Selecione o modelo</option>');
    $('select[name="anoFabricacao"]').val('');
    $('select[name="anoModelo"]').val('');
    $('select[name="versao"]').val('');
    $('input[name="outraVersao"]').val('');
    $('select[name="motor"]').val('');
    $('select[name="idValvula"]').val('');
    $('select[name="cor"]').val('');
    $('select[name="portas"]').val('');
    $('select[name="combustivel"]').val('');
    var cambioSel = $('label[for="cambio"]').closest('.form-group').find('select');
    if (cambioSel.length) cambioSel.val('');
}

export const seletor = '.c-criar-anuncio.a-index';
export const callback = ($) => {
    var stepsContainer = $('.step-container.step-veiculo');
    var lastSavedData;
    var dataWithError;
    var formWithError;

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
                    '#dados-basicos,.step-dados,.step-preco,.step-mais-informacoes',
                ).serialize();
            }, 500);
        })
        .on('steps-loaded', function () {
            marcaModelo($('#form_dadosVeiculo'));
            var placaAtual = $('#placaVeiculo').val();
            $('form[name="form_dadosVeiculo"]')
                .find('input[name="placa"]')
                .blur(function () {
                    var placaInput = $(this);
                    var placa = placaInput.val() || '';

                    placaInput.removeClass('is-invalid is-valid');
                    if (!placa || placa.length < 7) {
                        return;
                    }
                    if (placaAtual.toUpperCase() == placa.toUpperCase()) {
                        return;
                    }
                    BtnContinuar.disable();
                    $.ajax({
                        type: 'GET',
                        url: '/carro/placa-disponivel/' + placa.toLowerCase(),
                        dataType: 'json',
                        success: function (response) {
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

                            if (
                                response.historicoCarro &&
                                response.historicoCarro !== null &&
                                Object.values(response.historicoCarro.dados_veiculo).length !== 0
                            ) {
                                if (Array.isArray(response.historicoCarro.dados_veiculo)) {
                                    var dados_veiculo = response.historicoCarro.dados_veiculo[0];
                                    if (dados_veiculo !== undefined) {
                                        response.historicoCarro.dados_veiculo = dados_veiculo;
                                    } else {
                                        response.historicoCarro.dados_veiculo = false;
                                    }
                                }
                            } else {
                                response.historicoCarro = response.historicoCarro || {};
                                response.historicoCarro.dados_veiculo = false;
                            }

                            if (response.historicoCarro && response.historicoCarro.dados_veiculo) {
                                var historico = response.historicoCarro;
                                var dadosVeiculo = historico.dados_veiculo;
                                var fipe = historico.fipe || null;

                                // LIMPAR campos da placa anterior
                                resetFormFields($);

                                // Parser FIPE
                                var parsed = {};
                                if (fipe && fipe.modelo) {
                                    parsed = parseFipeModelo(fipe.modelo);
                                }

                                // Ano Fabricação / Ano Modelo
                                var anoModelo = dadosVeiculo.ano_modelo;
                                if (dadosVeiculo.ano_fabricacao && (parseInt(dadosVeiculo.ano_fabricacao) || false)) {
                                    $('select[name="anoFabricacao"]').val(dadosVeiculo.ano_fabricacao);
                                }
                                if (dadosVeiculo.ano_modelo && (parseInt(dadosVeiculo.ano_modelo) || false)) {
                                    if (anoModelo === '0' || anoModelo === null) {
                                        anoModelo = dadosVeiculo.ano_fabricacao;
                                    }
                                    $('select[name="anoModelo"]').val(anoModelo);
                                }
                                setTimeout(function () {
                                    $('select[name="anoModelo"]').trigger('change', [
                                        false,
                                        $('[name="caracteristicaVeiculo"]').val(),
                                    ]);
                                }, 0);

                                // Cor — normalizada, gênero flexível
                                if (dadosVeiculo.cor) {
                                    var corBase = normalizeStr(dadosVeiculo.cor).replace(/[aoe]$/, '');
                                    $('select[name="cor"] option').each(function () {
                                        var option = $(this);
                                        var optBase = normalizeStr(option.val() || option.text()).replace(/[aoe]$/, '');
                                        if (optBase && corBase && optBase === corBase) {
                                            option.prop('selected', true);
                                            return false;
                                        }
                                    });
                                }

                                // Combustível — mapa Denatran + normalizado
                                var combustivelAlvo = parsed.combustivel || dadosVeiculo.combustivel;
                                if (combustivelAlvo) {
                                    var combNorm = normalizeStr(combustivelAlvo);
                                    var combMapeado = denatranCombMap[combNorm];
                                    setSelectByMatch($, 'combustivel', combMapeado || combustivelAlvo);
                                }

                                // Cilindradas (motos)
                                var motoCilindradas = $('input[name="motoCilindradas"');
                                if (motoCilindradas.length && dadosVeiculo.cilindradas) {
                                    motoCilindradas.val(dadosVeiculo.cilindradas).trigger('input');
                                }

                                // MARCA
                                var marcaFonte = (fipe && fipe.marca) ? fipe.marca : dadosVeiculo.marca;
                                $('select[name="idMarca"] option:not(:selected)').prop('disabled', false).removeClass('hide');
                                $('select[name="idMarca"] option:selected').prop('selected', false);
                                var options = $('select[name="idMarca"] option');

                                if (marcaFonte) {
                                    var marcaLower = marcaFonte.toLowerCase().trim();
                                    var marcaNormalizada = marcaAliases[marcaLower] || marcaLower;
                                    var marcaEncontrada = false;

                                    options.each(function (_k, v) {
                                        var option = $(v);
                                        var marca = option.html().trim().toLowerCase();
                                        if (marca === marcaNormalizada || marca === marcaLower) {
                                            option.prop('selected', true);
                                            $('select[name="idMarca"]').trigger('change');
                                            $('select[name="idMarca"] option:selected').prop('disabled', false).removeClass('hide');
                                            $('select[name="idMarca"] option:not(:selected)').prop('disabled', true).addClass('hide');
                                            marcaEncontrada = true;
                                            return false;
                                        }
                                    });

                                    if (!marcaEncontrada) {
                                        options.each(function (_k, v) {
                                            var option = $(v);
                                            var marca = option.html().trim().toLowerCase();
                                            if (marca.indexOf(marcaNormalizada) > -1 || marcaNormalizada.indexOf(marca) > -1) {
                                                option.prop('selected', true);
                                                $('select[name="idMarca"]').trigger('change');
                                                $('select[name="idMarca"] option:selected').prop('disabled', false).removeClass('hide');
                                                $('select[name="idMarca"] option:not(:selected)').prop('disabled', true).addClass('hide');
                                                return false;
                                            }
                                        });
                                    }
                                }

                                // MODELO + CAMPOS EXTRAS (polling espera AJAX)
                                var modeloAlvo = parsed.remaining || dadosVeiculo.modelo;
                                var _modeloPollCount = 0;
                                var _modeloPoll = setInterval(function () {
                                    _modeloPollCount++;
                                    var modeloOptions = $('select[name="modeloCarro"] option');
                                    if (modeloOptions.length <= 1 && _modeloPollCount < 50) return;
                                    clearInterval(_modeloPoll);

                                    $('select[name="modeloCarro"] option:selected').prop('selected', false);
                                    var bestMatchIndex = -1;
                                    var bestMatchLength = 0;
                                    var bestMatchName = '';
                                    var alvoLower = modeloAlvo.toLowerCase();

                                    // Passo 1: prefixo exato
                                    modeloOptions.each(function (k, v) {
                                        var option = $(v);
                                        var optName = option.html().trim();
                                        if (!optName || !option.val()) return;
                                        var optLower = optName.toLowerCase();
                                        if (alvoLower.indexOf(optLower) === 0 && optLower.length > bestMatchLength) {
                                            var nextChar = alvoLower.charAt(optLower.length);
                                            if (nextChar === '' || nextChar === ' ') {
                                                bestMatchIndex = k;
                                                bestMatchLength = optLower.length;
                                                bestMatchName = optName;
                                            }
                                        }
                                    });

                                    // Passo 2: substring contida
                                    if (bestMatchIndex === -1) {
                                        modeloOptions.each(function (k, v) {
                                            var option = $(v);
                                            var optName = option.html().trim();
                                            if (!optName || !option.val()) return;
                                            var escaped = optName.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                                            if (RegExp(escaped, 'i').test(modeloAlvo) && optName.length > bestMatchLength) {
                                                bestMatchIndex = k;
                                                bestMatchLength = optName.length;
                                                bestMatchName = optName;
                                            }
                                        });
                                    }

                                    // Passo 3: todas palavras da option existem no alvo
                                    if (bestMatchIndex === -1) {
                                        var alvoWords = alvoLower.split(/\s+/);
                                        modeloOptions.each(function (k, v) {
                                            var option = $(v);
                                            var optName = option.html().trim();
                                            if (!optName || !option.val()) return;
                                            var optWords = optName.toLowerCase().split(/\s+/);
                                            var allFound = optWords.every(function (w) {
                                                return alvoWords.indexOf(w) > -1;
                                            });
                                            if (allFound && optWords.length > bestMatchLength) {
                                                bestMatchIndex = k;
                                                bestMatchLength = optWords.length;
                                                bestMatchName = optName;
                                            }
                                        });
                                    }

                                    // Passo 4: normalizado sem hífens
                                    // "IPACE" ↔ "I-Pace", "TCROSS" ↔ "T-Cross", "HRV" ↔ "HR-V"
                                    if (bestMatchIndex === -1) {
                                        var alvoNorm = normalizeStr(modeloAlvo).replace(/\s+/g, '');
                                        modeloOptions.each(function (k, v) {
                                            var option = $(v);
                                            var optName = option.html().trim();
                                            if (!optName || !option.val()) return;
                                            var optNorm = normalizeStr(optName).replace(/\s+/g, '');
                                            if (alvoNorm.indexOf(optNorm) === 0 && optNorm.length > bestMatchLength) {
                                                bestMatchIndex = k;
                                                bestMatchLength = optNorm.length;
                                                bestMatchName = optName;
                                            }
                                        });
                                    }

                                    if (bestMatchIndex > -1) {
                                        $(modeloOptions[bestMatchIndex]).prop('selected', true);
                                    }

                                    // Derivar versão removendo palavras do modelo
                                    var versaoDerivada = '';
                                    if (bestMatchName) {
                                        var optWordsNorm = normalizeStr(bestMatchName).split(/\s+/);
                                        var remainingWords = modeloAlvo.split(/\s+/).filter(function (w) {
                                            var wNorm = normalizeStr(w);
                                            var idx = optWordsNorm.indexOf(wNorm);
                                            if (idx > -1) {
                                                optWordsNorm.splice(idx, 1);
                                                return false;
                                            }
                                            // "IPACE" = "i"+"pace" joined
                                            var joined = optWordsNorm.join('');
                                            if (joined && wNorm === joined) {
                                                optWordsNorm = [];
                                                return false;
                                            }
                                            return true;
                                        });
                                        versaoDerivada = remainingWords.join(' ');
                                    }

                                    // Campos extras via parser FIPE
                                    if (parsed.motor) setSelectByMatch($, 'motor', parsed.motor);
                                    if (parsed.valvulas) setSelectByMatch($, 'idValvula', String(parsed.valvulas));

                                    if (parsed.cambio) {
                                        var cambioSelect = $('label[for="cambio"]').closest('.form-group').find('select');
                                        if (cambioSelect.length) {
                                            var cambioAlvo = parsed.cambio.toLowerCase();
                                            cambioSelect.find('option').each(function () {
                                                var opt = $(this);
                                                var optText = opt.text().trim().toLowerCase();
                                                if (optText === cambioAlvo || optText.indexOf(cambioAlvo) > -1 || cambioAlvo.indexOf(optText) > -1) {
                                                    opt.prop('selected', true);
                                                    return false;
                                                }
                                            });
                                        }
                                    }

                                    if (parsed.portas) setSelectByMatch($, 'portas', String(parsed.portas));

                                    // Versão
                                    if (versaoDerivada) {
                                        setTimeout(function () {
                                            var versaoSelect = $('select[name="versao"]');
                                            var temOpcoesReais = versaoSelect.find('option').filter(function () {
                                                var val = $(this).val();
                                                return val && val !== '' && val !== '-1';
                                            }).length > 0;

                                            var versaoSetada = false;
                                            if (temOpcoesReais) {
                                                versaoSetada = setSelectByMatch($, 'versao', versaoDerivada);
                                            }
                                            if (!versaoSetada) {
                                                versaoSelect.val('-1').trigger('change');
                                                var textoVersao = versaoDerivada +
                                                    (parsed.tracao ? ' ' + parsed.tracao : '') +
                                                    (parsed.turbo ? ' Turbo Intercooler' : '');
                                                var inputOutraVersao = $('input[name="outraVersao"]');
                                                if (inputOutraVersao.length) {
                                                    inputOutraVersao.val(textoVersao);
                                                }
                                            }
                                        }, 1500);
                                    }
                                }, 100);
                            }

                            BtnContinuar.enable();
                        },
                        error: function () {},
                    });
                });
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
            '#dados-basicos,.step-dados,.step-preco,.step-mais-informacoes,.step-opcionais',
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
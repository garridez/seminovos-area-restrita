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
// FIPE Parser — extrai dados estruturados da string de modelo FIPE
// Ex: "Pajero 3.2 4x4 T.I. Dies. 5p Aut"
//   → { modeloNome: "Pajero", motor: "3.2", tracao: "4X4",
//        turbo: true, combustivel: "Diesel", portas: 5, cambio: "Automático" }
// ============================================================
function parseFipeModelo(fipeStr) {
    var remaining = fipeStr.trim();
    var result = {};

    // Carroceria (CD = Cabine Dupla, CS = Cabine Simples, CE = Cabine Estendida)
    remaining = remaining.replace(/\b(CD|CS|CE)\b/g, function (m) {
        result.carroceria = m.toUpperCase();
        return '';
    });

    // Câmbio
    var cambioMap = {
        'aut': 'Automático',
        'man': 'Manual',
        'cvt': 'CVT',
    };
    remaining = remaining.replace(/\b(Aut|Man|CVT)\b\.?/i, function (m) {
        var key = m.replace('.', '').toLowerCase();
        result.cambio = cambioMap[key] || m;
        return '';
    });

    // Portas (ex: "5p", "4p", "3p", "2p")
    remaining = remaining.replace(/\b(\d)p\b/i, function (_m, n) {
        result.portas = parseInt(n, 10);
        return '';
    });

    // Combustível
    var combMap = {
        'dies': 'Diesel',
        'diesel': 'Diesel',
        'flex': 'Flex',
        'gas': 'Gasolina',
        'gasolina': 'Gasolina',
        'elet': 'Elétrico',
        'elétrico': 'Elétrico',
        'híb': 'Híbrido',
        'gnv': 'GNV',
        'alc': 'Álcool',
        'álcool': 'Álcool',
    };
    remaining = remaining.replace(
        /\b(Dies|Diesel|Flex|Gas|Gasolina|Elet|Elétrico|Híb|Híbrido|GNV|Alc|Álcool)\b\.?/i,
        function (m) {
            var key = m.replace('.', '').toLowerCase();
            result.combustivel = combMap[key] || m;
            return '';
        },
    );

    // Turbo / Aspiração (T.I. = Turbo Intercooler)
    remaining = remaining.replace(/\bT\.?\s?I\.?\b/gi, function () {
        result.turbo = true;
        return '';
    });
    remaining = remaining.replace(/\bTurbo\b/gi, function () {
        result.turbo = true;
        return '';
    });

    // Tração
    remaining = remaining.replace(/\b(4x[24]|AWD|FWD|RWD|2WD)\b/i, function (m) {
        result.tracao = m.toUpperCase();
        return '';
    });

    // Motor / Cilindrada (ex: "3.2", "1.0", "2.0")
    remaining = remaining.replace(/\b(\d\.\d)\b/, function (m) {
        result.motor = m;
        return '';
    });

    // Válvulas (ex: "8V", "16V", "24V")
    remaining = remaining.replace(/\b(\d{1,2})[Vv]\b/, function (_m, n) {
        result.valvulas = parseInt(n, 10);
        return '';
    });

    // Tokens técnicos conhecidos (limpar)
    remaining = remaining.replace(
        /\b(MPI|MPFI|DOHC|SOHC|VVT|VVTi|VTEC|TSI|TDI|HDI|CDI|CGI|GDI|EcoBoost|BlueHDi|JTD)\b/gi,
        function (m) {
            result.tecnologia = result.tecnologia || [];
            result.tecnologia.push(m.toUpperCase());
            return '';
        },
    );

    // O que sobrou = modelo + versão (limpar espaços)
    remaining = remaining.replace(/\s{2,}/g, ' ').replace(/^\s+|\s+$/g, '');

    // Separar modelo de versão/trim
    var words = remaining.split(' ');
    var knownVersions = [
        'LT', 'LTZ', 'LTZ2', 'LS', 'LX', 'EX', 'EXL', 'DX', 'GL', 'GLS', 'GLX',
        'SE', 'SEL', 'SR', 'SV', 'SL', 'XE', 'XRE', 'XRV', 'XRS', 'XEI', 'XLS', 'XLT',
        'Comfort', 'Plus', 'Sense', 'Life', 'Active', 'Style', 'Evolution', 'Limited',
        'Premium', 'Titanium', 'Ghia', 'HPE', 'HPE-S', 'Highline', 'Comfortline', 'Trendline',
        'Adventure', 'Endurance', 'Volcano', 'Freedom', 'Longitude', 'Overland',
        'Outdoor', 'Urban', 'Intense', 'Iconic', 'Launch', 'Edition',
    ];
    var knownVersionsLower = knownVersions.map(function (v) { return v.toLowerCase(); });
    var versionPattern = /^[A-Z]{2,5}$/;

    var modelWords = [];
    var versionWords = [];
    var foundVersion = false;

    for (var i = 0; i < words.length; i++) {
        var word = words[i];
        if (!word) continue;
        var isKnownVersion = knownVersionsLower.indexOf(word.toLowerCase()) > -1;
        var looksLikeVersion = !foundVersion && versionPattern.test(word) && word.length <= 5;

        if (!foundVersion && !isKnownVersion && !looksLikeVersion) {
            modelWords.push(word);
        } else {
            foundVersion = true;
            versionWords.push(word);
        }
    }

    result.modeloNome = modelWords.join(' ');
    if (versionWords.length) {
        result.versao = versionWords.join(' ');
    }

    return result;
}

// ============================================================
// Mapeamento de siglas Denatran/RENAVAM → nome comercial
// ============================================================
var marcaAliases = {
    'mmc': 'mitsubishi',
    'gm': 'chevrolet',
    'gm - chevrolet': 'chevrolet',
    'chevrolet - gm': 'chevrolet',
    'vw': 'volkswagen',
    'vw - volkswagen': 'volkswagen',
    'fiat': 'fiat',
    'ford': 'ford',
    'toyota': 'toyota',
    'hob': 'honda',
    'honda': 'honda',
    'mbenz': 'mercedes-benz',
    'mercedes-benz': 'mercedes-benz',
    'm.benz': 'mercedes-benz',
    'bmw': 'bmw',
    'nissan': 'nissan',
    'jac': 'jac',
    'jac motors': 'jac',
    'lr': 'land rover',
    'land rover': 'land rover',
    'hyundai': 'hyundai',
    'kia': 'kia',
    'kia motors': 'kia',
    'peug': 'peugeot',
    'peugeot': 'peugeot',
    'ren': 'renault',
    'renault': 'renault',
    'citr': 'citroën',
    'citroën': 'citroën',
    'citroen': 'citroën',
    'jeep': 'jeep',
    'sub': 'subaru',
    'subaru': 'subaru',
    'volvo': 'volvo',
    'suzuki': 'suzuki',
    'chery': 'chery',
    'jag': 'jaguar',
    'jaguar': 'jaguar',
    'porsche': 'porsche',
    'audi': 'audi',
    'ram': 'ram',
    'dodge': 'dodge',
    'chrysler': 'chrysler',
    'chev': 'chevrolet',
    'cad': 'cadillac',
    'byd': 'byd',
    'gwm': 'gwm',
    'caoa chery': 'caoa chery',
    'troller': 'troller',
};

// ============================================================
// Helper: tenta setar o valor de um <select> por match parcial
// Retorna true se encontrou
// ============================================================
function setSelectByMatch($, selectName, targetValue, form) {
    var container = form || $(document);
    var select = container.find('select[name="' + selectName + '"]');
    if (!select.length || !targetValue) return false;

    var target = targetValue.toLowerCase().trim();
    var found = false;

    // Primeira tentativa: match exato
    select.find('option').each(function () {
        var option = $(this);
        var optVal = option.text().trim().toLowerCase();
        if (optVal === target) {
            option.prop('selected', true);
            found = true;
            return false;
        }
    });

    if (found) return true;

    // Segunda tentativa: match parcial (contém)
    select.find('option').each(function () {
        var option = $(this);
        var optVal = option.text().trim().toLowerCase();
        if (optVal.indexOf(target) > -1 || target.indexOf(optVal) > -1) {
            option.prop('selected', true);
            found = true;
            return false;
        }
    });

    return found;
}

export const seletor = '.c-criar-anuncio.a-index';
export const callback = ($) => {
    var stepsContainer = $('.step-container.step-veiculo');
    var lastSavedData;
    var dataWithError;
    var formWithError;

    //($('#form_dadosVeiculo'))
    $('.anuncio-steps')
        .on('steps-loaded', function () {
            // Para esperar as máscaras serem aplicadas
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
            /* @todo COLOCAR A FUNÇÃO DE VALIDAR PLACA DURANTE O TAB */
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

                                // ========================================
                                // Parser FIPE: extrair dados estruturados
                                // ========================================
                                var parsed = {};
                                if (fipe && fipe.modelo) {
                                    parsed = parseFipeModelo(fipe.modelo);
                                }

                                // ========================================
                                // Ano Fabricação / Ano Modelo
                                // ========================================
                                var anoModelo = dadosVeiculo.ano_modelo;

                                if (
                                    dadosVeiculo.ano_fabricacao &&
                                    (parseInt(dadosVeiculo.ano_fabricacao) || false)
                                ) {
                                    $('select[name="anoFabricacao"]').val(
                                        dadosVeiculo.ano_fabricacao,
                                    );
                                }

                                if (
                                    dadosVeiculo.ano_modelo &&
                                    (parseInt(dadosVeiculo.ano_modelo) || false)
                                ) {
                                    if (anoModelo === '0' || anoModelo === null) {
                                        anoModelo = dadosVeiculo.ano_fabricacao;
                                    }
                                    $('select[name="anoModelo"]').val(anoModelo);
                                }

                                // Trigger para buscar versão
                                setTimeout(function () {
                                    $('select[name="anoModelo"]').trigger('change', [
                                        false,
                                        $('[name="caracteristicaVeiculo"]').val(),
                                    ]);
                                }, 0);

                                // ========================================
                                // Cor — prioriza dados_veiculo (já vem limpo)
                                // ========================================
                                var corSelecionada = dadosVeiculo.cor
                                    .toLowerCase()
                                    .slice(0, -1);
                                $('select[name="cor"] option:selected').prop('selected', false);
                                var options = $('select[name="cor"] option');
                                options.each(function (_k, v) {
                                    var option = $(v);
                                    var cor = option.val().toLowerCase().slice(0, -1);
                                    if (corSelecionada == cor) {
                                        option.prop('selected', true);
                                        return false;
                                    }
                                });

                                // ========================================
                                // Combustível — prioriza parsed FIPE, fallback dados_veiculo
                                // ========================================
                                $('select[name="combustivel"] option:selected').prop(
                                    'selected',
                                    false,
                                );
                                var combustivelAlvo = parsed.combustivel || dadosVeiculo.combustivel;
                                if (combustivelAlvo) {
                                    setSelectByMatch($, 'combustivel', combustivelAlvo);
                                }

                                // ========================================
                                // Cilindradas (motos)
                                // ========================================
                                var motoCilindradas = $('input[name="motoCilindradas"');
                                if (
                                    motoCilindradas.length &&
                                    dadosVeiculo.cilindradas
                                ) {
                                    motoCilindradas
                                        .val(dadosVeiculo.cilindradas)
                                        .trigger('input');
                                }

                                // ========================================
                                // MARCA — prioriza FIPE, fallback dados_veiculo + aliases
                                // ========================================
                                var marcaFonte = (fipe && fipe.marca)
                                    ? fipe.marca
                                    : dadosVeiculo.marca;

                                $('select[name="idMarca"] option:not(:selected)')
                                    .prop('disabled', false)
                                    .removeClass('hide');
                                $('select[name="idMarca"] option:selected').prop('selected', false);
                                options = $('select[name="idMarca"] option');

                                if (marcaFonte) {
                                    var marcaLower = marcaFonte.toLowerCase().trim();
                                    // Resolver alias (MMC → mitsubishi, GM → chevrolet, etc.)
                                    var marcaNormalizada = marcaAliases[marcaLower] || marcaLower;

                                    var marcaEncontrada = false;
                                    options.each(function (_k, v) {
                                        var option = $(v);
                                        var marca = option.html().trim().toLowerCase();
                                        if (
                                            marca === marcaNormalizada ||
                                            marca === marcaLower
                                        ) {
                                            option.prop('selected', true);
                                            $('select[name="idMarca"]').trigger('change');
                                            $('select[name="idMarca"] option:selected')
                                                .prop('disabled', false)
                                                .removeClass('hide');
                                            $('select[name="idMarca"] option:not(:selected)')
                                                .prop('disabled', true)
                                                .addClass('hide');
                                            marcaEncontrada = true;
                                            return false;
                                        }
                                    });

                                    // Se não encontrou nem por alias, tenta match parcial
                                    if (!marcaEncontrada) {
                                        options.each(function (_k, v) {
                                            var option = $(v);
                                            var marca = option.html().trim().toLowerCase();
                                            if (
                                                marca.indexOf(marcaNormalizada) > -1 ||
                                                marcaNormalizada.indexOf(marca) > -1
                                            ) {
                                                option.prop('selected', true);
                                                $('select[name="idMarca"]').trigger('change');
                                                $('select[name="idMarca"] option:selected')
                                                    .prop('disabled', false)
                                                    .removeClass('hide');
                                                $('select[name="idMarca"] option:not(:selected)')
                                                    .prop('disabled', true)
                                                    .addClass('hide');
                                                return false;
                                            }
                                        });
                                    }
                                }

                                // ========================================
                                // MODELO + CAMPOS EXTRAS
                                // Espera o select de modelos ser populado
                                // (carrega via AJAX após o trigger de marca)
                                // ========================================
                                var modeloAlvo = parsed.modeloNome || dadosVeiculo.modelo;

                                // Polling: espera as options do modelo carregarem (max 5s)
                                var _modeloPollCount = 0;
                                var _modeloPoll = setInterval(function () {
                                    _modeloPollCount++;
                                    var modeloOptions = $('select[name="modeloCarro"] option');

                                    // Ainda só tem "Selecione o modelo"? Espera mais
                                    if (modeloOptions.length <= 1 && _modeloPollCount < 50) {
                                        return; // tenta de novo em 100ms
                                    }
                                    clearInterval(_modeloPoll);

                                    // --- Setar modelo ---
                                    $('select[name="modeloCarro"] option:selected').prop(
                                        'selected',
                                        false,
                                    );
                                    var matchRegex = -1;
                                    modeloOptions.each(function (k, v) {
                                        var option = $(v);
                                        var modelo = option.html().trim();
                                        if (!modelo) return;
                                        var regex = RegExp(modelo, 'i');
                                        if (regex.test(modeloAlvo)) {
                                            if (matchRegex > -1) {
                                                var previosOption = $(modeloOptions[matchRegex])
                                                    .html()
                                                    .trim();
                                                matchRegex =
                                                    previosOption.length > modelo.length
                                                        ? matchRegex
                                                        : k;
                                            } else {
                                                matchRegex = k;
                                            }
                                        }
                                    });
                                    if (matchRegex > -1) {
                                        $(modeloOptions[matchRegex]).prop('selected', true);
                                    }

                                    // --- Campos extras via parser FIPE ---

                                    // Motor — name="motor", options text: "3.2", "1.0", etc.
                                    if (parsed.motor) {
                                        setSelectByMatch($, 'motor', parsed.motor);
                                    }

                                    // Válvulas — name="idValvula", options text: "8", "16", etc.
                                    if (parsed.valvulas) {
                                        setSelectByMatch($, 'idValvula', String(parsed.valvulas));
                                    }

                                    // Câmbio — name="checkboxacessorios[]" (compartilhado!)
                                    // Localiza pelo label[for="cambio"] para pegar o select correto
                                    if (parsed.cambio) {
                                        var cambioSelect = $('label[for="cambio"]')
                                            .closest('.form-group')
                                            .find('select');
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

                                    // Portas — name="portas", options text: "5 Portas", etc.
                                    if (parsed.portas) {
                                        setSelectByMatch($, 'portas', String(parsed.portas));
                                    }

                                    // Versão — name="versao", carrega via AJAX após anoModelo
                                    if (parsed.versao) {
                                        setTimeout(function () {
                                            var versaoSetada = setSelectByMatch(
                                                $,
                                                'versao',
                                                parsed.versao,
                                            );
                                            if (!versaoSetada) {
                                                // Seleciona "Outra versão" (value="-1")
                                                $('select[name="versao"]').val('-1').trigger('change');
                                                // Preenche o input de texto
                                                var inputOutraVersao = $('input[name="outraVersao"]');
                                                if (inputOutraVersao.length) {
                                                    inputOutraVersao.val(
                                                        parsed.versao +
                                                            (parsed.turbo ? ' Turbo Intercooler' : ''),
                                                    );
                                                }
                                            }
                                        }, 1500);
                                    }
                                }, 100); // poll a cada 100ms
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

        // Salvar todo o formulario anterior as fotos aqui
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

            /**
             * @TODO Corrigir o "/carro" para o valor correto
             */
            url: '/carro/dados',
            data: dataSerialized,
            dataType: 'json',
            success: function (data) {
                ajaxProcessing = false;
                /**
                 * Atribui o valor do veiculo no form, caso a pessoa volte e edite,
                 *      na hora de salvar é enviado o id do veículo e assim é feita a edição
                 */
                data = data.data;
                if (data) {
                    var idVeiculo = data[0].idVeiculo;

                    $('#dados-basicos .idVeiculo').val(idVeiculo);
                    $('#dados-basicos .idAnuncioVeiculo').val(data[0].idAnuncio);
                    $('#dados-basicos .idPlano').val(5); // Plano inativo. TODO: Dinamizar.
                    $('#dados-basicos .placaVeiculo').val(placa);

                    $('#form_dadosVeiculo')
                        .find('input:not([type="submit"]):not([type="button"]), select')
                        .prop('disabled', true)
                        .prop('readonly', true);

                    var path = window.location.pathname.match(/^\/[a-z]+/).input + '/' + idVeiculo;
                    window.history.pushState(null, null, path);
                }
                // Guarda o que foi serializado para garantir que não vai salvar dados que não foram alterados
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
<?php

namespace AreaRestrita\Service;

use DateTimeImmutable;

/**
 * Certificado Documental para o PDF de "Baixar PDF" (certificados/index.phtml).
 *
 * CÓPIA do formatador da API do app (site: module/ApiProxy/src/App/Vendedor/CertificadoDocumental.php):
 * o PDF daqui e o do app usam o mesmo formato. Mudou lá, mude aqui também.
 *
 * Vem da api2 (GET /veiculos-certificados/:placa). O JSON da 9mobile chega em três
 * formatos (body.data, body.responseJSON ou data); aqui vira sempre a mesma estrutura:
 * quadro de avisos + seções com itens (nome/valor), texto e registros (leilão, roubo).
 * Valor vazio volta null (o app mostra "Não informado"). Só o dono do anúncio vê.
 */
final class CertificadoDocumental
{
    public const EMISSOR = '9mobile';

    /** Quadro de avisos, na ordem e com os textos da área restrita. */
    private const AVISOS = [
        'restricao'  => ['flagRestricao', 'Possui restrições?'],
        'sinistro'   => ['flagSinistro', 'Possui indício de sinistro?'],
        'leilao'     => ['flagLeilao', 'Possui registro de leilão?'],
        'debitos'    => ['flagDebitos', 'Possui débitos?'],
        'rouboFurto' => ['flagFurto', 'Possui registro de roubo?'],
    ];

    private const PENDENTE = 'Consulta ainda pendente';

    public const INFORMACOES = [
        'Certificado emitido por órgãos parceiros da empresa 9mobile.',
        'Algumas informações podem não estar disponíveis no momento da consulta devido a instabilidades da base de'
            . ' dados dos prestadores de serviço, mas serão reconsultadas (sem custo adicional) e enviadas assim que possível.',
        'Não nos responsabilizamos por informações errôneas enviadas, tanto do usuário quanto da empresa 9mobile.',
    ];

    /**
     * @param array $c Linha de veiculos_certificados (data da api2).
     */
    public static function formatar(array $c, string $placa): array
    {
        $dados = self::dadosBasicos($c['dadosBasicos'] ?? null);
        $estadual = self::mapa($dados['baseEstadual'] ?? null);
        $nacional = self::mapa($dados['baseNacional'] ?? null);
        $sinistro = self::mapa($dados['indicioSinistro'] ?? null);
        $leilao = self::mapa($dados['leilao'] ?? null);
        $furto = self::mapa($dados['rouboFurto'] ?? null);

        $avisos = [];
        foreach (self::AVISOS as $chave => [$campo, $nome]) {
            $avisos[] = ['chave' => $chave, 'nome' => $nome, 'consta' => (string) ($c[$campo] ?? '0') === '1'];
        }

        $secoes = [
            [
                'chave'  => 'veiculo',
                'titulo' => 'Dados principais do veículo',
                'itens'  => $dados === null ? [] : [
                    self::item('Placa', strtoupper($placa)),
                    self::item('Chassi', $dados['chassi'] ?? null, 'chassi'),
                    self::item('Renavam', $dados['renavam'] ?? null, 'renavam'),
                    self::item('Núm. motor', $dados['numMotor'] ?? $dados['motor'] ?? null, 'motor'),
                    self::item('Tipo de veículo', $dados['tipoVeiculo'] ?? $dados['especie'] ?? null),
                    self::item('Marca/Modelo', $dados['marcaModelo'] ?? null),
                    self::item('Ano fabricação', $dados['anoFabricacao'] ?? null),
                    self::item('Ano modelo', $dados['anoModelo'] ?? null),
                    self::item('Combustível', $dados['combustivel'] ?? null),
                    self::item('Cor', $dados['cor'] ?? null),
                    self::item('Município', $dados['nomeMunicipioEmplacamento'] ?? $dados['municipio'] ?? null),
                    self::item('UF', $dados['uf'] ?? null),
                ],
                'texto'  => $dados === null ? self::PENDENTE : null,
            ],
            [
                'chave'  => 'risco',
                'titulo' => 'Risco de recusa em comercialização',
                'itens'  => [],
                'texto'  => $dados === null
                    ? self::PENDENTE
                    : (self::texto($dados['analiseRisco']['parecer'] ?? null) ?? 'Não há dados'),
            ],
            [
                'chave'  => 'estadual',
                'titulo' => 'Cadastro estadual',
                'itens'  => $estadual === null ? [] : [
                    self::item('IPVA', $estadual['existeDebitoIpva'] ?? null),
                    self::item('Licenciamento', $estadual['existeDebitoLicenciamento'] ?? null),
                    self::item('DPVAT', $estadual['existeDebitoDpvat'] ?? null),
                    self::item('Multas', $estadual['existeDebitoMulta'] ?? null),
                    self::item('Restrições 1', $estadual['outrasRestricoes1'] ?? null),
                    self::item('Restrições 2', $estadual['outrasRestricoes2'] ?? null),
                    self::item('Restrições 3', $estadual['outrasRestricoes3'] ?? null),
                    self::item('Restrições 4', $estadual['outrasRestricoes4'] ?? null),
                    self::item('RENAJUD', $estadual['restricaoRenajud'] ?? null),
                    self::item('Administrativa', $estadual['restricaoAdminisrativa'] ?? $estadual['restricaoAdministrativa'] ?? null),
                    self::item('Tributária', $estadual['restricaoTributaria'] ?? null),
                    self::item('Judicial', $estadual['restricaoJudicial'] ?? null),
                    self::item('Guincho', $estadual['restricaoGuincho'] ?? null),
                    self::item('Financeira', $estadual['restricaoFinanceira'] ?? null),
                    self::item('Comunicação de venda', $estadual['comunicacaoVenda'] ?? null),
                ],
                'texto'  => $estadual === null ? self::PENDENTE : null,
            ],
            [
                'chave'  => 'nacional',
                'titulo' => 'Cadastro nacional',
                'itens'  => $nacional === null ? [] : [
                    self::item('Restrições 1', $nacional['restricao1'] ?? null),
                    self::item('Restrições 2', $nacional['restricao2'] ?? null),
                    self::item('Restrições 3', $nacional['restricao3'] ?? null),
                    self::item('Restrições 4', $nacional['restricao4'] ?? null),
                    self::item('RENAJUD', $nacional['indicadorRestricaoRenajud'] ?? null),
                    self::item('Financiadora', $nacional['restricaoFinanciadora'] ?? null),
                    self::item('Financiamento', $nacional['restricaoFinanciamento'] ?? null),
                    self::item('Comunicação de venda', $nacional['indicadorComunicacaoVendas'] ?? null),
                ],
                'texto'  => $nacional === null ? self::PENDENTE : null,
            ],
            [
                'chave'  => 'sinistro',
                'titulo' => 'Indício de sinistro',
                'itens'  => [],
                'texto'  => $sinistro === null
                    ? self::PENDENTE
                    : (self::texto($sinistro['descricao'] ?? null) ?? 'Não informado'),
            ],
            self::secaoLeilao($leilao),
            self::secaoFurto($furto),
        ];

        $apontamentos = array_values(array_map(
            static fn (array $a): string => $a['chave'],
            array_filter($avisos, static fn (array $a): bool => $a['consta'])
        ));

        return [
            'placa'        => strtoupper($placa),
            'emitidoEm'    => self::data($c['dataCadastro'] ?? null),
            'emGeracao'    => empty($c['dataCadastro']),
            'pendente'     => $dados === null,
            'emissor'      => self::EMISSOR,
            'avisos'       => $avisos,
            'apontamentos' => $apontamentos,
            'secoes'       => $secoes,
            'informacoes'  => self::INFORMACOES,
        ];
    }

    /**
     * Tudo que o public/certificado-pdf/certificado-pdf.js precisa: o certificado e o veículo do anúncio.
     *
     * @param array $v Linha do veículo (api2 /veiculos/:id).
     */
    public static function paraPdf(array $c, string $placa, array $v, string $siteUrl = 'https://seminovos.com.br'): array
    {
        $anos = array_filter([
            (string) ($v['anoFabricacao'] ?? ''),
            (string) ($v['anoModelo'] ?? ''),
        ], static fn (string $a): bool => $a !== '' && $a !== '0');
        $id = (int) ($v['idVeiculo'] ?? 0);

        return self::formatar($c, $placa) + [
            'idVeiculo' => $id,
            'veiculo'   => trim(self::texto($v['marca'] ?? '') . ' ' . self::texto($v['modelo'] ?? '') . ' '
                . self::versaoVeiculo($v)),
            'anos'      => $anos ? implode('/', $anos) : null,
            'url'       => $id > 0 ? rtrim($siteUrl, '/') . '/' . $id : null,
        ];
    }

    /** Placas iguais sem traço e sem diferença de maiúscula ("abc-1234" = "ABC1234"). */
    public static function mesmaPlaca(string $a, string $b): bool
    {
        $n = static fn (string $p): string => strtoupper((string) preg_replace('/[^A-Za-z0-9]/', '', $p));

        return $n($a) !== '' && $n($a) === $n($b);
    }

    /**
     * Versão com motor e válvulas, sem repetir o que o anunciante escreveu nem pôr o motor do
     * cadastro ao lado de outro que ele escreveu; moto fica só com o texto do anunciante (a mesma
     * regra do site: module/Site/src/Helpers/VersaoVeiculo.php; mudou lá, mude aqui).
     */
    public static function versaoVeiculo(array $v): string
    {
        $caracteristica = self::texto($v['caracteristica'] ?? '') ?? '';
        // Anúncio salvo com o motor e as válvulas já na frente do texto ("2.0 8v 2.0 VVT-IE XEI").
        $prefixo = '/^(\d{1,2}[.,]\d)\s+\d{1,2}\s*v\s+(?=\1(?!\d))/iu';
        $caracteristica = preg_replace($prefixo, '', $caracteristica) ?? $caracteristica;
        if ((int) ($v['idCombustivel'] ?? 0) === 11 || (int) ($v['idTipo'] ?? $v['tipoVeiculo'] ?? 0) === 3) {
            return $caracteristica;
        }
        $vazio = static fn (string $x): bool => $x === '' || preg_match('/^0+([.,]0+)?$/', $x) === 1;
        $motor = self::texto($v['motor'] ?? '') ?? '';
        $valvulas = preg_replace('/\s*v$/i', '', self::texto($v['valvula'] ?? '') ?? '') ?? '';
        $valvulas = $vazio($valvulas) || preg_match('/(?<!\w)\d{1,2}\s*(?:v(?!\pL)|v[aá]lv)/iu', $caracteristica)
            ? ''
            : $valvulas . 'v';

        // Motor escrito no texto, com o que vem grudado ("1.0L", "2.0T", "1.8/1.8"): um dígito antes
        // do ponto ("24.280" e "45.5 mil km" não são motor) ou o próprio motor do cadastro.
        $motores = '\d[.,]\d';
        if (preg_match('/^(\d{1,2})[.,](\d)$/', $motor, $mm)) {
            $motores .= '|' . $mm[1] . '[.,]' . $mm[2];
        }
        $motorNoTexto = '/(?<![\w.,])(?:' . $motores . ')(?!\d)[^\s,;()]*?(?=\.*(?:[\s,;()]|$))/u';

        // O anunciante já escreveu um motor: vale o dele; as válvulas entram logo depois.
        if (preg_match($motorNoTexto, $caracteristica, $m, PREG_OFFSET_CAPTURE)) {
            if ($valvulas === '') {
                return $caracteristica;
            }
            $fim = $m[0][1] + strlen($m[0][0]);

            return trim(substr($caracteristica, 0, $fim) . ' ' . $valvulas . substr($caracteristica, $fim));
        }
        $partes = [$vazio($motor) ? '' : $motor, $valvulas, $caracteristica];

        return trim(implode(' ', array_filter($partes, static fn (string $p): bool => $p !== '')));
    }

    private static function secaoLeilao(?array $leilao): array
    {
        $registros = [];
        foreach ((array) ($leilao['registros'] ?? []) as $r) {
            if (!is_array($r)) {
                continue;
            }
            $registros[] = [
                self::item('Data', self::dataBr($r['dataLeilao'] ?? null)),
                self::item('Id do leilão', $r['_id'] ?? null),
                self::item('Lote', $r['lote'] ?? null),
                self::item('Condição', $r['condicaoGeral'] ?? null),
                self::item('Comitente', $r['comitente'] ?? null),
                self::item('Leiloeiro', $r['leiloeiro'] ?? null),
            ];
        }

        return [
            'chave'     => 'leilao',
            'titulo'    => 'Leilão',
            'itens'     => [],
            'texto'     => $leilao === null
                ? self::PENDENTE
                : (self::texto($leilao['descricao'] ?? null) ?? 'Não existe registro de indício de leilão'),
            'registros' => $registros,
        ];
    }

    private static function secaoFurto(?array $furto): array
    {
        $registros = [];
        foreach ((array) ($furto['historico'] ?? []) as $r) {
            if (!is_array($r)) {
                continue;
            }
            $registros[] = [
                self::item('Data', self::dataBr($r['dataOcorrencia'] ?? null)),
                self::item('Município', $r['municipioOcorrencia'] ?? null),
                self::item('UF', $r['ufOcorrencia'] ?? null),
                self::item('Ocorrência', $r['ocorrencia'] ?? null),
                self::item('Declaração', $r['declaracao'] ?? null),
            ];
        }

        return [
            'chave'     => 'rouboFurto',
            'titulo'    => 'Ocorrência de roubo/furto',
            'itens'     => [],
            'texto'     => $furto === null
                ? self::PENDENTE
                : (!empty($furto['constaOcorrenciaAtiva'])
                    ? 'Consta ocorrência de roubo ativa'
                    : 'Não consta ocorrência de roubo ativa'),
            'registros' => $registros,
        ];
    }

    /** Os três formatos que a 9mobile já devolveu (mesma ordem da área restrita). */
    public static function dadosBasicos(mixed $bruto): ?array
    {
        if (is_string($bruto) && $bruto !== '') {
            $bruto = json_decode($bruto, true);
        }
        if (!is_array($bruto)) {
            return null;
        }
        foreach ([$bruto['body']['data'] ?? null, $bruto['body']['responseJSON'] ?? null, $bruto['data'] ?? null] as $d) {
            if (is_array($d) && $d) {
                return $d;
            }
        }

        return null;
    }

    /**
     * @param string|null $sensivel chassi, renavam ou motor: o app esconde parte no PDF compartilhado.
     */
    private static function item(string $nome, mixed $valor, ?string $sensivel = null): array
    {
        $item = ['nome' => $nome, 'valor' => self::texto($valor)];
        if ($sensivel !== null) {
            $item['sensivel'] = $sensivel;
        }

        return $item;
    }

    private static function mapa(mixed $valor): ?array
    {
        return is_array($valor) && $valor ? $valor : null;
    }

    private static function texto(mixed $valor): ?string
    {
        if (is_bool($valor)) {
            return $valor ? 'Sim' : 'Não';
        }
        if (!is_scalar($valor)) {
            return null;
        }
        $t = (string) $valor;
        if ($t !== '' && !mb_check_encoding($t, 'UTF-8')) {
            $t = mb_convert_encoding($t, 'UTF-8', 'ISO-8859-1');
        }
        $t = trim(preg_replace('/\s+/u', ' ', $t) ?? '');

        return $t === '' ? null : $t;
    }

    private static function data(mixed $valor): ?string
    {
        $t = self::texto($valor);
        if ($t === null) {
            return null;
        }
        $d = DateTimeImmutable::createFromFormat('!Y-m-d', substr($t, 0, 10));

        return $d ? $d->format('Y-m-d') : null;
    }

    /** "2023-05-10", "10/05/2023" ou "10/05/2023 14:00" viram "10/05/2023" (como a área restrita). */
    private static function dataBr(mixed $valor): ?string
    {
        $t = self::texto($valor);
        if ($t === null) {
            return null;
        }
        $ts = strtotime(str_replace('/', '-', $t));

        return $ts === false ? $t : date('d/m/Y', $ts);
    }
}

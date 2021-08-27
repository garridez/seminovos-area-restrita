<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestrita\Controller;

use AreaRestrita\Model\Cadastros;
use AreaRestrita\Form as Form;
use AreaRestrita\Form\MeusDados;
use AreaRestrita\Model\Pagamentos;
use AreaRestrita\Model\Planos;
use AreaRestrita\Model\ServicosAdicionais;
use AreaRestrita\Model\SiteHospedado;
use SnBH\ApiClient\Client as ApiClient;
use Zend\View\Model\ViewModel;
use SnBH\Common\Helper\StringFuncs;

class XmlController extends AbstractActionController
{

    /**
     * Index para importar XML
     */
    public function indexAction()
    {
        $apiClient = $this->getApiClient();

        /* @var $siteHospedadoModel siteHospedado */
        $siteHospedado = $this->getContainer()->get(SiteHospedado::class);

        $dadosSiteHospedado = $siteHospedado->get();

        $cadastro = $this->getContainer()->get(Cadastros::class);
        $dadosCadastro = $cadastro->getCurrent();

        $planos = [
            'nitro' => $dadosCadastro['nitro'],
            'turbo' => $dadosCadastro['turbo'],
            'simples' => $dadosCadastro['simples'],
        ];

        return new ViewModel([
            'planos' => $planos,
        ]);
    }


    /**
     * Tela para edição dos dados dos veículos contidos no xml
     * 
     * @return View
     */
    public function dadosVeiculosAction()
    {
        $request = $this->getRequest();
        $veiculos = [];

        $inputs = $request->getPost()->toArray();
        $veiculosComErro = [];

        // valida link XML
        if (substr($inputs['href'], -3) != 'xml') {
            die('Não é um arquivo .xml válido');
            // return $this->redirect()->toUrl('/xml');
        }

        /* @var $siteHospedadoModel siteHospedado */
        $cadastro = $this->getContainer()->get(Cadastros::class);
        $dadosCadastro = $cadastro->getCurrent();

        // Numero de veículos que podem ser cadastrados no plano selecionado
        $quantidadeAnunciosPlano = $dadosCadastro[$inputs['plano']];

        switch ($inputs['plano']) {
            case 'simples':
                $idPlano = 1;
                break;
            
            case 'turbo':
                $idPlano = 2;
                break;
            
            case 'nitro':
                $idPlano = 3;
                break;
            
            case 'nitro + home|nitro+home':
                $idPlano = 4;
                break;
            
            case 'basico':
                $idPlano = 5;
                break;
        }

        // Busca os acessorios carro
        $acessoriosCarroApi = $this->getApiClient()->acessoriosGet(['idTipo' => 1]);
        // Remove acentos 
        $acessoriosCarroApi->dataSemAceontos = 
            array_map(function($array) {
                $array['acessorio'] = $this->removerAcentos($array['acessorio']);
                return $array;
            }, $acessoriosCarroApi->data);

        // Busca os acessorios moto
        $acessoriosMotoApi = $this->getApiClient()->acessoriosGet(['idTipo' => 3]);
        // Remove acentos 
        $acessoriosMotoApi->data = 
            array_map(function($array) {
                $array['acessorio'] = $this->removerAcentos($array['acessorio']);
                return $array;
            }, $acessoriosMotoApi->data);


        // Busca as Marcas
        $marcasApi = $this->getApiClient()->marcasGet();
        $marcasApi->data = 
            array_map(function($array) {
                $array['marca'] = $this->removerAcentos($array['marca']);
                return $array;
            }, $marcasApi->data);

        // Busca modelos
        /*$modelosApi = $this->getApiClient()->modelosGet();
        $modelosApi->data = 
            array_map(function($array) {
                $array['modelo'] = $this->removerAcentos($array['modelo']);
                return $array;
            }, $modelosApi->data); */       

        
        // Carrega o XML
        $xmlDoc = new \DOMDocument();
        $xmlDoc->load($inputs['href']);        
        $document = $xmlDoc->documentElement;
        $quantidadeVeiculosCadastrados = 0;

        foreach ($document->childNodes as $ad) {
            $veiculo = [];
            $veiculo['idCadastro'] = $dadosCadastro['idCadastro'];
            $veiculo['troca'] = 1;

            foreach ($ad->childNodes as $item) {
                // $item->nodeName => $item->nodeValue
                // TITLE => fiat idea sporting 1.8 flex 16v 5p

                switch ($item->nodeName) {
                    case 'TITLE': // caracteristica
                        $veiculo['caracteristica'] = $item->nodeValue;
                        break;
                    
                    case 'CATEGORY': // Tipo veículo
                        $veiculo['tipo'] = strtolower($item->nodeValue) == 'carro' ? 1 : (strtolower($item->nodeValue) == 'moto' ? 3 : 2);
                        $veiculo['tipoVeiculo'] = strtolower($item->nodeValue) == 'carro' ? 1 : (strtolower($item->nodeValue) == 'moto' ? 3 : 2);
                        break;
                    
                    case 'DESCRIPTION': // observações
                        $veiculo['observacoes'] = $item->nodeValue;
                        break;

                    case 'MOTOR': // MOTOR
                        $veiculo['motor'] = $item->nodeValue;
                        break;
                    
                    case 'ACCESSORIES': // Acessorios
                        $veiculo['listaAcessorios'] = [];

                        // Remove acentos
                        $arrayAcessoriosXml = array_map(function ($item) {
                            return $this->removerAcentos($item);
                        }, explode(',', $item->nodeValue));

                        $acessoriosApi = $veiculo['tipoVeiculo'] == 1 ? $acessoriosCarroApi->dataSemAceontos : $acessoriosMotoApi->data;

                        foreach ($arrayAcessoriosXml as $acessorioXml) {
                            // Verifica se existe no array de acessorios da API
                            foreach ($acessoriosApi as $acessorioApi) {
                                if ($acessorioApi['acessorio'] == $acessorioXml) {
                                    array_push($veiculo['listaAcessorios'], $acessorioApi['idAcessorio']);
                                    break;
                                }
                            }
                        }

                        break;
                    
                    case 'MAKE': // Marca
                        foreach ($marcasApi->data as $marcaApi) {
                            if (preg_match("/($item->nodeValue)/i", $marcaApi['marca'])) {
                                $veiculo['marca'] = $item->nodeValue;
                                $veiculo['idMarca'] = $marcaApi['idMarca'];
                                // Busca modelos
                                $modelos =  $this->getApiClient()->modelosGet(['idMarca' => $marcaApi['idMarca']]);
                                $veiculo['modelos'] = $modelos->data;
                                break;
                            } else if (preg_match("/($item->nodeValue)/i", str_replace(' ', '', $marcaApi['marca']))) {
                                $veiculo['marca'] = $item->nodeValue;
                                $veiculo['idMarca'] = $marcaApi['idMarca'];
                                // Busca modelos
                                $modelos =  $this->getApiClient()->modelosGet(['idMarca' => $marcaApi['idMarca']]);
                                $veiculo['modelos'] = $modelos->data;
                                break;
                            }
                        }
                        break;
                    
                    case 'MODEL': // Modelo
                        $modeloXml = $item->nodeValue;

                        foreach ($modelos->data as $modeloApi) {
                            // Escapa a "/" nos modelos
                            $modeloApiString = preg_replace("/\//", "\/", $modeloApi['modelo']);

                            $modeloApiSemEspaco = StringFuncs::removeCaractersEspecias($modeloApiString);

                            // Cria 2 palavras com o modelo se possivel: ka hatch => [ka, hatch]
                            $modeloApiArray = explode(" ", $modeloApiString);
                            $palavra1 = $modeloApiArray[0];

                            if (strlen($palavra1) < 2) {
                                $palavra2 = isset($modeloApiArray[1]) && strlen($palavra1) >= 2 ? $modeloApiArray[1] : 'xzxzxzxz';
                            }

                            if (ctype_alnum($palavra1)) {
                                // 1º tenta dar match na string inteira do modelo da API
                                if (preg_match("/[a-zA-Z0-9]/", $modeloXml) && preg_match("/\s?^($modeloApiString)(.*)?/", $modeloXml)) {
                                    $veiculo['modeloCarro'] = $modeloApi['idModelo'];
                                    break;

                                // 2º tenta dar match na primeira palavra do modelo se ele tiver mais q 2 caracteres
                                } else if (strlen($palavra1) >= 2 && preg_match("/\s?^($palavra1)/", $modeloXml)) {
                                    $veiculo['modeloCarro'] = $modeloApi['idModelo'];
                                    break;
                                    
                                // 3º tenta dar match na segunda palavra do modelo
                                } else if (isset($palavra2) && preg_match("/\s?($palavra2)/", $modeloXml)) {
                                    $veiculo['modeloCarro'] = $modeloApi['idModelo'];
                                    break;
                                    // 4º tenta dar match na primeira palavra do modelo sem espaço se ele tiver mais q 2 caracteres
                                } else if (preg_match("/[a-zA-Z0-9]/", $modeloXml) && preg_match("/\s?^($modeloApiSemEspaco)(.*)?/", $modeloXml)) {
                                    $veiculo['modeloCarro'] = $modeloApi['idModelo'];
                                    break;
                                // 5º tenta dar match na primeira palavra do modelo de forma simples
                                } else if (preg_match("/$palavra1/i", $modeloXml)) {
                                    $veiculo['modeloCarro'] = $modeloApi['idModelo'];
                                    break;
                                }
                            }
                        }
                        break;
                    
                    case 'YEAR': // Ano
                        $veiculo['anoModelo'] = $item->nodeValue;
                        break;
                    
                    case 'FABRIC_YEAR': // Ano Fabricação
                        $veiculo['anoFabricacao'] = $item->nodeValue;
                        break;
                    
                    case 'MILEAGE': // Ano Fabricação
                        $veiculo['kilometragem'] = $item->nodeValue;
                        $veiculo['veiculoZeroKm'] = $item->nodeValue == 0 ? 1 : 0;
                        break;
                    
                    case 'PLATE': // Placa
                        $veiculo['placa'] = $item->nodeValue;
                        break;
                    
                    case 'DOORS': // Portas
                        $veiculo['portas'] = $item->nodeValue;
                        break;
                    
                    case 'COLOR': // Cor
                        $veiculo['cor'] = $item->nodeValue;
                        break;
                    
                    case 'PRICE': // Valor
                        $veiculo['valor'] = number_format($item->nodeValue, 2, ',', '.');
                        break;
                    
                    case 'FUEL': // Combustivel
                        switch ($this->removerAcentos(strtolower($item->nodeValue))) {
                            case 'flex|bi combustivel|bi-combustivel':
                                $veiculo['combustivel'] = 2;
                                break;
                            
                            case 'alcool':
                                $veiculo['combustivel'] = 1;
                                break;
                            
                            case 'diesel':
                                $veiculo['combustivel'] = 3;
                                break;
                            
                            case 'gasolina':
                                $veiculo['combustivel'] = 4;
                                break;
                            
                            case 'gas|kit gas|kit-gas':
                                $veiculo['combustivel'] = 6;
                                break;
                            
                            case 'gasolina e gas|gasolina+gas|gasolina + gas|gasolina + kit gas|gasolina + kit-gas':
                                $veiculo['combustivel'] = 5;
                                break;
                            
                            case 'tetra fuel|tetra-fuel|tetra|tetra combustivel':
                                $veiculo['combustivel'] = 7;
                                break;
                            
                            case 'gasolina e eletrico|gasolina+eletrico|gasolina + eletrico|gasolina + elétrico|gasolina e elétrico':
                                $veiculo['combustivel'] = 8;
                                break;
                            
                            case 'alcool e gas|alcool+gas|alcool + gas|alcool + kit gas|alcool + kit-gas':
                                $veiculo['combustivel'] = 9;
                                break;
                            
                            case 'bi combustivel e gas|bi combustivel+gas|bi combustivel + gas|bi combustivel + kit gas|bi combustivel + kit-gas':
                                $veiculo['combustivel'] = 10;
                                break;
                            
                            case 'eletrico|elétrico':
                                $veiculo['combustivel'] = 11;
                                break;
                            
                            default:
                                $veiculo['combustivel'] = 4;
                                break;
                        }
                        break;

                    case 'IMAGES':
                        $arrayFotos = [];
                        $count = 0;
                        
                        foreach ($item->childNodes as $imagem) {
                            if ($count > 11) {
                                break;
                            }
                            $veiculo['imagens'][] = $imagem->nodeValue;
                            $count++;
                        }
                        break;
                }
            }

            // IPVA, Status ativo e ID Plano
            $veiculo['idPlano'] = $idPlano;
            $veiculo['nomePlano'] = $inputs['plano'];
                
            $veiculos[] = $veiculo;
        }

        $viewModel = new ViewModel([
            'veiculos' => $veiculos,
            'marcas' => $marcasApi->data,
            'acessoriosCarroApi' => $acessoriosCarroApi->data,
        ]);

        $viewModel->setTemplate('area-restrita/xml/dados-veiculos.phtml');

        return $viewModel;
    }



    /**
     * Importa o XML
     * 
     * @return ViewModel
     */
    public function salvarAction()
    {
        $request = $this->getRequest();

        $quantidadeVeiculosCadastrados = 0;
        $veiculosComErro = [];
        $nomePlano = '';
        $veiculos = $request->getPost()->toArray();


        /* @var $siteHospedadoModel siteHospedado */
        $cadastro = $this->getContainer()->get(Cadastros::class);
        $dadosCadastro = $cadastro->getCurrent();

        
        foreach ($veiculos['veiculos'] as $placa => $veiculo) {
            // Numero de veículos que podem ser cadastrados no plano selecionado
            $quantidadeAnunciosPlano = $dadosCadastro[$veiculo['nomePlano']];
            $nomePlano = $veiculo['nomePlano'];

            // Formata o valor do dinheiro para o Banco
            $veiculo['valor'] = preg_replace(['/\./', '/\,/'], ['', '.'], $veiculo['valor']);

            if (isset($veiculo['observacoes']) && $veiculo['observacoes']) {
                // Devido ao erro de codificação com alguns carecteres especiais, é truncado para 700
                $auxTexto = str_replace("\r\n","",StringFuncs::removerAcentos($veiculo['observacoes']));
                if(strlen($auxTexto) > 700){
                    $veiculo['observacoes'] = mb_substr($veiculo['observacoes'], 0, 700,'UTF8');
                }
            }
            
            // Faz upload das imagens
            $arrayFotos = [];
            
            foreach ($veiculo['imagens'] as $url) {
                $extensao = substr($url, -4);
                $name = uniqid();
                $img = '/tmp/' . $name . $extensao;

                file_put_contents($img, file_get_contents($url));
                
                array_push($arrayFotos, $img);
            }

            // Envia o veiculo
            try {
                $veiculo['flagIpva'] = 1;
                $veiculo['idStatus'] = 2; // Ativo

                $apiClient = $this->getApiClient();
                
                if ($quantidadeVeiculosCadastrados >= $quantidadeAnunciosPlano) {
                    $veiculosComErro[$veiculo['placa']] = "A quantidade de veículos cadastrados atingiu o limite do plano. ({$quantidadeAnunciosPlano})";
                } else {
                    // Salva o veículo
                    $retorno = $apiClient->veiculosPost($veiculo)->json();

                    if ($retorno['status'] != 200) {
                        $veiculosComErro[$veiculo['placa']] = $retorno['detail'];
                    } else {
                        $quantidadeVeiculosCadastrados++;
    
                        // Salva imagens do veículo
                        $imagem = [
                            'idVeiculo' => $retorno['data'][0]['idVeiculo'],
                            'idTipo' => 1,
                            'flagS3' => 0,
                        ];
    
                        $imagem[$apiClient::KEY_FILES] = [
                            'fotos' => $arrayFotos
                        ];
    
                        // Faz upload da imagem
                        $retorno = $apiClient->veiculosFotosPost($imagem)->json();
                    }
                }

            } catch (\Exception $e) {
                $veiculosComErro[$veiculo['placa']] = $e->getMessage();
            }

        }

        $anunciosRestantes = $quantidadeAnunciosPlano - $quantidadeVeiculosCadastrados;

        // Remove a quantidade de veículos cadastrados do plano escolhido
        $resPut = $this->getApiClient()->cadastrosPut(
            [
                $nomePlano => $anunciosRestantes,
                'tipoCadastro' => $dadosCadastro['tipoCadastro']
            ], 
            $dadosCadastro['idCadastro']);

        $view = new ViewModel([
            'veiculosComErro' => $veiculosComErro
        ]);
        $view->setTemplate('area-restrita/xml/relatorio-xml.phtml');

        return $view;
    }

    /**
     * Remove acentos e espaços em branco desnecessários
     * 
     * @param String $string
     * @return String
     */
    public function removerAcentos($string)
    {
        $string = trim($string);

        $string = preg_replace(
            array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/", "/(ç)/", "/(Ç)/"),
            explode(" ","a A e E i I o O u U n N c C"),
            $string
        );

        return strtolower($string);
    }
}

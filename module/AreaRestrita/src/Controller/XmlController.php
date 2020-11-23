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
     * Importa o XML
     * 
     * @return ViewModel
     */
    public function salvarAction()
    {
        $request = $this->getRequest();

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
        $acessoriosCarroApi->data = 
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

        // Busca modelos
        $modelosApi = $this->getApiClient()->modelosGet();
        $modelosApi->data = 
            array_map(function($array) {
                $array['modelo'] = $this->removerAcentos($array['modelo']);
                return $array;
            }, $modelosApi->data);        

        
            
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
                    
                    case 'ACCESSORIES': // Acessorios
                        $veiculo['listaAcessorios'] = [];

                        // Remove acentos
                        $arrayAcessoriosXml = array_map(function ($item) {
                            return $this->removerAcentos($item);
                        }, explode(',', $item->nodeValue));

                        $acessoriosApi = $veiculo['tipoVeiculo'] == 1 ? $acessoriosCarroApi->data : $acessoriosMotoApi->data;

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
                        $veiculo['marca'] = $item->nodeValue;
                        break;
                    
                    case 'MODEL': // Modelo
                        $modeloXml = explode(' ', $item->nodeValue)[0];
                        
                        foreach ($modelosApi->data as $modeloApi) {

                            $modeloTemp = explode(' ', $modeloApi['modelo'])[0];

                            if ($modeloXml == $modeloTemp) {
                                $veiculo['modeloCarro'] = $modeloApi['idModelo'];
                                break;
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
                        $veiculo['carroPortas'] = $item->nodeValue;
                        break;
                    
                    case 'COLOR': // Cor
                        $veiculo['cor'] = $item->nodeValue;
                        break;
                    
                    case 'PRICE': // Valor
                        $veiculo['valor'] = $item->nodeValue;
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
                            
                            default:
                                $veiculo['combustivel'] = 4;
                                break;
                        }
                        break;

                    case 'IMAGES':
                        $arrayFotos = [];

                        foreach ($item->childNodes as $imagem) {
                            $url = $imagem->nodeValue; // URL da imagem
                            $extensao = substr($url, -4);
                            $name = uniqid();
                            $img = '/tmp/' . $name . $extensao;

                            file_put_contents($img, file_get_contents($url));
                            
                            array_push($arrayFotos, $img);
                        }
                        break;
                }
            }

            // Envia o veiculo
            try {
                
                $veiculo['flagIpva'] = 1;
                $veiculo['idPlano'] = $idPlano;
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
                $inputs['plano'] => $anunciosRestantes,
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

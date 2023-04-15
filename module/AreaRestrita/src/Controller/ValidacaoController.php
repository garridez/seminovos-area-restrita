<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestrita\Controller;

use Laminas\View\Model\ViewModel;
use SnBH\ApiClient\Client as ApiClient;

class ValidacaoController extends AbstractActionController
{

    public function validaEmailAction()
    {
        $encryptedText = $this->params('dados');

        $dataRes = $this->getApiClient()->crypterGet([
            'data' => $encryptedText
        ]);
       
        if ($dataRes->status !== 200) {
            return new ViewModel(["sucesso" => false]);        
        }
        $data = json_decode((string) $dataRes->getData(), true);
        
        $cadastros = $this->getApiClient()->cadastrosGet([
            'considerarInativo' => 1], $data['idCadastro'], false)->getData()[0];

        $sucesso = false;

        if($cadastros['idStatus'] == 2){
            $dados = [
                'considerarInativo' => 1,
                'idStatus' => 1,
                'tipoCadastro' => $cadastros['tipoCadastro']
                ];
            $resPut = $this->getApiClient()->cadastrosPut($dados, $data['idCadastro']);
            if ($resPut->status === 200) {
                $sucesso = true;
            }
        }
        return new ViewModel(["sucesso" => $sucesso]);        
    }
}

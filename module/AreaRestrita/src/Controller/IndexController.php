<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestrita\Controller;

use Zend\View\Model\ViewModel;
use AreaRestrita\Model\Cadastros;

class IndexController extends AbstractActionController
{

    public function indexAction()
    {

        /* @var $cadastrosModel Cadastros */
        $cadastrosModel = $this->getContainer()->get(Cadastros::class);

        // Busca os dados do cadastro
        $dadosCadastro = $cadastrosModel->getCurrent();


        $novoNome = $dadosCadastro['responsavelNome'] . ' ' . rand(0, 10);

        $resPut = $cadastrosModel->put([
            'tipoCadastro' => 2,
            'responsavelNome' => $novoNome
        ]);
        if ($resPut->status === 200) {
            var_dump('Salvo com sucesso');
        } else {
            var_dump($resPut->detail);
        }



        return new ViewModel();
    }
}

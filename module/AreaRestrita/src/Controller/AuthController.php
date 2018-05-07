<?php

namespace AreaRestrita\Controller;

use AreaRestrita\Form\Login;
use SnBH\ApiClient\Client;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AuthController extends AbstractActionController
{

    public function loginAction()
    {
        /* @var $sm ServiceLocatorInterface */
        global $sm;
        $particularForm = new Login\ParticularForm();
        $revendaForm = new Login\RevendaForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();

            /* @var $form \Zend\Form\Form */
            $form = null;
            $particularForm;
            foreach ([$particularForm, $revendaForm] as $form) {
                if ($form->getName() === $post['type']) {
                    break;
                }
            }

            $form->setData($post);

            if ($form->isValid()) {
                /* @var $apiClient \SnBH\ApiClient\Client */
                $apiClient = $sm->get(Client::class);
                $data = $form->getData();                
                $data['acao'] = 'login';

                /* @var $resLoginPost \SnBH\ApiClient\Response */
                $resLoginPost = $apiClient->loginPost($data);
                var_dump($resLoginPost->json());

                die;
            }
        }
        
        return new ViewModel([
            'particularForm' => $particularForm,
            'revendaForm' => $revendaForm
        ]);
    }
}

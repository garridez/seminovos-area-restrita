<?php

namespace AreaRestritaAnuncio\Form\Veiculo;

use Zend\Form\Form;
use Zend\Form\Element;

class PrecoForm extends Form
{

    public function __construct($name = 'form_precoVeiculo', $options = array())
    {
        parent::__construct($name, $options);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'valor',
            'options' => [
                'label' => 'Valor',
            ],
            'attributes' => [
                'required' => true,
            ]
        ]);
        $this->add([
            'type' => Element\Submit::class,
            'name' => 'submit',
            'attributes' => [
                'value' => 'Cadastrar',
            ],
        ]);

        $this->configureInputFilter();
    }

    protected function configureInputFilter()
    {
        $inputFilter = $this->getInputFilter();
        $inputFilter->add([
            'name' => 'valor',
            'required' => true,
        ]);
    }

}
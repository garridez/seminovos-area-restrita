<?php

namespace AreaRestritaAnuncio\Form\Veiculo;

use Laminas\Form\Form;
use Laminas\Form\Element;

class MaisInformacoesForm extends Form
{

    public function __construct($name = 'form_maisInformacoesVeiculo', $options = [])
    {
        parent::__construct($name, $options);

        $this->add([
            'type' => Element\Checkbox::class,
            'name' => 'idTroca',
            'options' => [
                'label' => 'Aceita Troca??',
                'use_hidden_element' => true,
                'checked_value' => '4',
                'unchecked_value' => '1',
            ],
        ]);

        $this->add([
            'type' => Element\Checkbox::class,
            'name' => 'delivery',
            'options' => [
                'label' => 'Este veículo pode ser levado até o cliente?',
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0',
            ],
        ]);


        $this->add([
            'type' => Element\Checkbox::class,
            'name' => 'aceitaProposta',
            'options' => [
                'label' => 'Aceitar proposta por e-mail.',
            ],
            'attributes' => [
                'value' => 'yes',
            ],
        ]);

        // $this->add([
        //     'type' => Element\Checkbox::class,
        //     'name' => 'aceitaLigacao',
        //     'options' => [
        //         'label' => 'Aceitar contato por telefone.',
        //     ],
        //     'attributes' => [
        //         'value' => 'yes',
        //     ],
        // ]);
        $this->add([
            'type' => Element\Checkbox::class,
            'name' => 'aceitaChat',
            'options' => [
                'label' => 'Aceitar contato por chat.',
            ],
            'attributes' => [
                'value' => 'yes',
            ],
        ]);

        $this->add([
            'type' => Element\Checkbox::class,
            'name' => 'aceitaLigacao',
            'options' => [
                'label' => 'Exibir meu(s) telefone(s)?',
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0',
            ],
        ]);

        $this->add([
            'type' => Element\Checkbox::class,
            'name' => 'termo',
            'options' => [
                'label' => 'Li e aceito os termos de responsabilidade e a política de privacidade.',
            ],
            'attributes' => [
                'required' => true,
            ],
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
        $inputFilter->add([
            'name' => 'idTroca',
            'required' => true,
        ]);
        $inputFilter->add([
            'name' => 'termo',
            'required' => true,
        ]);
    }
}

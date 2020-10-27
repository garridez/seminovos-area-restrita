<?php

namespace AreaRestritaAnuncio\Form\Veiculo;

use Zend\Form\Form;
use Zend\Form\Element;

class MaisInformacoesForm extends Form
{

    public function __construct($name = 'form_maisInformacoesVeiculo', $options = array())
    {
        parent::__construct($name, $options);

        $this->add([
            'type' => Element\Textarea::class,
            'name' => 'observacoes',
            'options' => [
                'label' => 'Observações sobre o veículo',
            ],
            'attributes' => [
                'maxlength' => 700
            ]
        ]);
        $this->add([
            'type' => Element\Radio::class,
            'name' => 'idTroca',
            'options' => [
                'label' => 'Aceita Troca?',
                'value_options' => [
                    [
                        'label' => 'SIM',
                        'value' => '4',
                        'attributes' => [
                            'id' => 'idTroca_radio_sim',
                        ]
                    ],
                    [
                        'label' => 'NÃO',
                        'value' => '1',
                        'attributes' => [
                            'id' => 'idTroca_radio_nao',
                        ]
                    ],
                ],
            ],
            'attributes' => [
                'required' => true,
            ]
        ]);
        $this->add([
            'type' => Element\Radio::class,
            'name' => 'delivery',
            'options' => [
                'label' => 'Este veículo pode ser levado até o cliente?',
                'value_options' => [
                    [
                        'label' => 'SIM',
                        'value' => 1,
                        'attributes' => [
                            'id' => 'delivery_radio_sim',
                        ],
                    ],
                    [
                        'label' => 'NÃO',
                        'value' => 0,
                        'attributes' => [
                            'id' => 'delivery_radio_nao',
                        ],
                    ],
                ],
            ],
            'attributes' => [
                'id' => 'delivery_radio',
                'required' => false,
            ]
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
            'type' => Element\Radio::class,
            'name' => 'aceitaLigacao',
            'options' => [
                'label' => 'Exibir meu(s) telefone(s)?',
                'value_options' => [
                    [
                        'label' => 'SIM',
                        'value' => '1',
                        'selected' => true,
                        'attributes' => [
                            'id' => 'aceitaLigacao_radio_sim',
                        ]
                    ],
                    [
                        'label' => 'NÃO',
                        'value' => '0',
                        'selected' => false,
                        'attributes' => [
                            'id' => 'aceitaLigacao_radio_nao',
                        ]
                    ],
                ],
            ],
            'attributes' => [
                'required' => true,
            ]
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

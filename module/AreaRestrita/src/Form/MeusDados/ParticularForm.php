<?php

namespace AreaRestrita\Form\MeusDados;

use Laminas\Form\Element;
use Laminas\Form\Form;
use Laminas\Validator\Hostname;
use Laminas\Validator\ValidatorChain;

class ParticularForm extends Form
{
    /**
     * @param string $name
     * @param array $options
     * @param bool $cadastroSimples
     */
    public function __construct($name = 'form_particularSite', $options = [], protected $cadastroSimples = false)
    {
        parent::__construct($name, $options);

        $this->add([
            'type' => Element\Text::class,
            'name' => 'responsavelNome',
            'options' => [
                'label' => 'Nome',
            ],
            'attributes' => [
                'required' => true,
                'readonly' => true,
                'class' => 'form-control',
                'placeholder' => 'Nome Sobrenome',
            ],
        ]);
        if (!$this->cadastroSimples) {
            $this->add([
                'type' => Element\Date::class,
                'name' => 'dataNascimento',
                'options' => [
                    'label' => 'Data de Nascimento',
                ],
                'attributes' => [
                    'class' => 'form-control',
                    'required' => true,
                    'min' => date('Y-m-d', strtotime('-100 year')),
                    'max' => date('Y-m-d'),
                ],
            ]);
        }

        $this->add([
            'type' => Element\Email::class,
            'name' => 'email',
            'options' => [
                'label' => 'E-mail',
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
                'placeholder' => 'seuemail@example.com.br',
            ],
        ]);
        if (!$this->cadastroSimples) {
            $this->add([
                'type' => Element\Text::class,
                'name' => 'rg',
                'options' => [
                    'label' => 'Rg',
                ],
                'attributes' => [
                    'required' => true,
                    'class' => 'form-control',
                    'placeholder' => 'AA-00.000.000',
                ],
            ]);
        }
        $this->add([
            'type' => Element\Text::class,
            'name' => 'cpfResponsavel',
            'options' => [
                'label' => 'CPF',
            ],
            'attributes' => [
                'required' => true,
                'readonly' => true,
                'class' => 'form-control',
                'placeholder' => '000.000.000-00',
                'data-mask' => '000.000.000-00',
            ],
        ]);

        $this->add([
            'type' => Element\Select::class,
            'name' => 'idEstado',
            'options' => [
                'label' => 'Estado',
                'value_options' => [
                    '' => 'Selecione',
                    '1' => 'Acre',
                    '2' => 'Alagoas',
                    '3' => 'Amazonas',
                    '4' => 'Amapá',
                    '5' => 'Bahia',
                    '6' => 'Ceará',
                    '7' => 'Distrito Federal',
                    '8' => 'Espírito Santo',
                    '9' => 'Goiás',
                    '10' => 'Maranhão',
                    '11' => 'Minas Gerais',
                    '12' => 'Mato Grosso do Sul',
                    '13' => 'Mato Grosso',
                    '14' => 'Pará',
                    '15' => 'Paraíba',
                    '16' => 'Pernambuco',
                    '17' => 'Piauí',
                    '18' => 'Paraná',
                    '19' => 'Rio de Janeiro',
                    '20' => 'Rio Grande do Norte',
                    '21' => 'Rondônia',
                    '22' => 'Roraima',
                    '23' => 'Rio Grande do Sul',
                    '24' => 'Santa Catarina',
                    '25' => 'Sergipe',
                    '26' => 'São Paulo',
                    '27' => 'Tocantins',
                ],
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
                'value' => 11,
            ],
        ]);
        $this->add([
            'type' => Element\Select::class,
            'name' => 'idCidade',
            'options' => [
                'label' => 'Cidade',
                'value_options' => [
                    '' => 'Selecione',
                    '2700' => 'BH',
                    '2922' => 'CONTAGEM',
                    '2' => 'BETIM',
                    '3' => 'NOVA LIMA',
                ],
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
            ],
        ]);
        if (!$this->cadastroSimples) {
            $this->add([
                'type' => Element\Text::class,
                'name' => 'telefone_1',
                'options' => [
                    'label' => 'Telefone Residencial',
                ],
                'attributes' => [
                    'class' => 'form-control',
                    'data-mask' => '(00) 0000-0000',
                    'placeholder' => '(__) ____-____',
                ],
            ]);
        }
        $this->add([
            'type' => Element\Text::class,
            'name' => 'telefone_2',
            'options' => [
                'label' => 'Celular',
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
                'data-mask' => '(00) 00000-0000',
                'placeholder' => '(__) _____-____',
            ],
        ]);

        $this->add([
            'type' => Element\Checkbox::class,
            'name' => 'telefone_2_is_wpp',
            'options' => [
                'label' => 'Whatsapp',
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0',
            ],
            'attributes' => [
                'value' => 1,
            ],
        ]);
        if (!$this->cadastroSimples) {
            $this->add([
                'type' => Element\Select::class,
                'name' => 'operadora_2',
                'options' => [
                    'label' => 'Operadora',
                    'value_options' => [
                        '' => 'Selecione',
                        '1' => 'OI',
                        '2' => 'TIM',
                        '3' => 'CLARO',
                        '4' => 'VIVO',
                        '5' => 'NEXTEL',
                    ],
                ],
                'attributes' => [
                    'required' => true,
                    'class' => 'form-control',
                ],
            ]);
        }
        if (!$this->cadastroSimples) {
            $this->add([
                'type' => Element\Text::class,
                'name' => 'telefone_3',
                'options' => [
                    'label' => 'Celular',
                ],
                'attributes' => [
                    'class' => 'form-control',
                    'data-mask' => '(00) 90000-0000',
                    'placeholder' => '(__) _____-____',
                ],
            ]);
            $this->add([
                'type' => Element\Select::class,
                'name' => 'operadora_3',
                'options' => [
                    'label' => 'Operadora',
                    'value_options' => [
                        '' => 'Selecione',
                        '1' => 'OI',
                        '2' => 'TIM',
                        '3' => 'CLARO',
                        '4' => 'VIVO',
                        '5' => 'NEXTEL',
                    ],
                ],
                'attributes' => [
                    'class' => 'form-control',
                ],
            ]);

            $this->add([
                'type' => Element\Checkbox::class,
                'name' => 'telefone_3_is_wpp',
                'options' => [
                    'label' => 'Whatsapp 3',
                    'checked_value' => '1',
                    'unchecked_value' => '0',
                ],
            ]);
        }

        $this->add([
            'type' => Element\Password::class,
            'name' => 'senha',
            'options' => [
                'label' => 'Nova Senha',
            ],
            'attributes' => [
                'required' => true,
                'class' => 'form-control',
            ],
        ]);
        if (!$this->cadastroSimples) {
            $this->add([
                'type' => Element\Password::class,
                'name' => 'confirmacaoSenha',
                'options' => [
                    'label' => 'Confirmar Senha',
                ],
                'attributes' => [
                    'required' => true,
                    'class' => 'form-control',
                ],
            ]);
        }

        $this->add([
            'type' => Element\Submit::class,
            'name' => 'submit',
            'attributes' => [
                'value' => 'Salvar',
                'class' => 'btn btn-success btn-cons',
            ],
        ]);

        $this->configureInputFilter();
    }

    protected function configureInputFilter(): void
    {
        $inputFilter = $this->getInputFilter();
        $inputFilter->add([
            'name' => 'responsavelNome',
            'required' => true,
        ]);
        if (!$this->cadastroSimples) {
            // campo não obrigatório, porém sem o required igual a false não funciona
            $inputFilter->add([
                'name' => 'dataNascimento',
                'required' => false,
            ]);
        }
        $inputFilter->add([
            'name' => 'email',
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim'],
                ['name' => 'StringToLower'],
            ],
            'validators' => [
                [
                    'name' => 'EmailAddress',
                    'options' => [
                        'allow' => Hostname::ALLOW_DNS,
                        'useMxCheck' => false,
                    ],
                ],
            ],
        ]);
        if (!$this->cadastroSimples) {
            $inputFilter->add([
                'name' => 'rg',
                'required' => true,
                'filters' => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StringToUpper'],
                ],
            ]);

            $inputFilter->add([
                'name' => 'cpfResponsavel',
                'required' => true,
            ]);

            $inputFilter->add([
                'name' => 'confirmacaoSenha',
                'required' => true,
            ]);
        }
        $inputFilter->add([
            'name' => 'idEstado',
            'required' => true,
        ]);
        // Reseta o validador de idCidade
        $inputFilter->get('idCidade')->setValidatorChain(new ValidatorChain());
        $inputFilter->add([
            'name' => 'idCidade',
            'required' => true,
        ]);
        if (!$this->cadastroSimples) {
            // campo não obrigatório, porém sem o required igual a false não funciona
            $inputFilter->add([
                'name' => 'operadora_2',
                'required' => false,
            ]);
            $inputFilter->add([
                'name' => 'operadora_1',
                'required' => false,
            ]);
            // campo não obrigatório, porém sem o required igual a false não funciona
            $inputFilter->add([
                'name' => 'operadora_3',
                'required' => false,
            ]);
        }
        $inputFilter->add([
            'name' => 'senha',
            'required' => true,
        ]);
    }
}

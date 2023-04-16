<?php

namespace AreaRestrita\Form\View\Helper;

use InvalidArgumentException;
use LogicException;
use TwbBundle\Form\View\Helper\TwbBundleFormRadio;
use Laminas\Form\ElementInterface;
use Laminas\Form\Element\Radio;
use Laminas\Form\Element\MultiCheckbox;
use Laminas\Form\View\Helper\FormRow;

class FormRadio extends TwbBundleFormRadio
{
    /**
     * @var mixed
     */
    public $labelAttributes;
    protected $separator = '</div><div class="radio radio-success mt-0 mb-0">';
    
    protected static $checkboxFormat = '<div class="radio radio-success mt-0 mb-0">%s</div>';

    public function render(ElementInterface $oElement)
    {
        $aElementOptions = $oElement->getOptions();

        if (isset($aElementOptions['disable-twb']) && $aElementOptions['disable-twb'] == true) {
            $sSeparator = $this->getSeparator();
            $this->setSeparator('');
            $sReturn = parent::render($oElement);
            $this->setSeparator($sSeparator);
            return $sReturn;
        }

        if (isset($aElementOptions['inline']) && $aElementOptions['inline'] == true) {
            $sSeparator = $this->getSeparator();
            $this->setSeparator('');
            $oElement->setLabelAttributes(['class' => 'radio-inline']);
            $sReturn = sprintf('%s', parent::render($oElement));
            $this->setSeparator($sSeparator);
            return $sReturn;
        }

        if (isset($aElementOptions['btn-group']) && $aElementOptions['btn-group'] != false) {

            $buttonClass = 'btn btn-primary';
            if (is_array($aElementOptions['btn-group']) && isset($aElementOptions['btn-group']['btn-class'])) {
                $buttonClass = $aElementOptions['btn-group']['btn-class'];
            }

        	$this->setSeparator('');
        	$oElement->setLabelAttributes(['class' => $buttonClass]);

        	return sprintf('<div class="btn-group" data-toggle="buttons">%s</div>', parent::render($oElement));
        }

        return sprintf(static::$checkboxFormat, parent::render($oElement));
    }
    
/*protected function renderOptions(
        MultiCheckbox $oElement,
        array $aOptions,
        array $aSelectedOptions,
        array $aAttributes
    ) {
        $iIterator = 0;
        $aGlobalLabelAttributes = $oElement->getLabelAttributes()? : $this->labelAttributes;
        $sMarkup = '';
        $oLabelHelper = $this->getLabelHelper();
        $aElementOptions = $oElement->getOptions();
        foreach ($aOptions as $key => $aOptionspec) {
            if (is_scalar($aOptionspec)) {
                $aOptionspec = array('label' => $aOptionspec, 'value' => $key);
            }

            $iIterator++;
            if ($iIterator > 1 && array_key_exists('id', $aAttributes)) {
                unset($aAttributes['id']);
            }

            //Option attributes
            $aInputAttributes = $aAttributes;
            if (isset($aOptionspec['attributes'])) {
                $aInputAttributes = \Laminas\Stdlib\ArrayUtils::merge($aInputAttributes, $aOptionspec['attributes']);
            }

            //Option value
            $aInputAttributes['value'] = isset($aOptionspec['value']) ? $aOptionspec['value'] : '';

            //Selected option
            if (in_array($aInputAttributes['value'], $aSelectedOptions)) {
                $aInputAttributes['checked'] = true;
            } elseif (isset($aOptionspec['selected'])) {
                $aInputAttributes['checked'] = !!$aOptionspec['selected'];
            } else {
                $aInputAttributes['checked'] = isset($aInputAttributes['selected']) && $aInputAttributes['type'] !== 'radio' && $aInputAttributes['selected'] != false;
            }

            //Disabled option
            if (isset($aOptionspec['disabled'])) {
                $aInputAttributes['disabled'] = !!$aOptionspec['disabled'];
            } else {
                $aInputAttributes['disabled'] = isset($aInputAttributes['disabled']) && $aInputAttributes['disabled'] != false;
            }

            if (!isset($aInputAttributes['id'])) {
            $aInputAttributes['id'] = trim($aInputAttributes['label']) . '_' . substr(md5($aInputAttributes['label']), 0, 4);
            }
            $aLabelAttributes['for'] = $aInputAttributes['id'];
            var_dump(__LINE__);
            //Render option
            $sOptionMarkup = sprintf('<input %s%s', $this->createAttributesString($aInputAttributes), $this->getInlineClosingBracket());

            //Option label
            $sLabel = isset($aOptionspec['label']) ? $aOptionspec['label'] : '';
            if ($sLabel) {
                $aLabelAttributes = $aGlobalLabelAttributes;
                if (isset($aElementOptions['btn-group']) && $aElementOptions['btn-group'] == true) {
                	if ($aInputAttributes['checked']) {
                		$aLabelAttributes['class'] = ((isset($aLabelAttributes['class'])) ? $aLabelAttributes['class'] : '') . ' active';
                	}
                }

                if (isset($aOptionspec['label_attributes'])) {
                    $aLabelAttributes = isset($aLabelAttributes) ? array_merge($aLabelAttributes, $aOptionspec['label_attributes']) : $aOptionspec['label_attributes'];
                }

                if (null !== ($oTranslator = $this->getTranslator())) {
                    $sLabel = $oTranslator->translate($sLabel, $this->getTranslatorTextDomain());
                }

                if (!($oElement instanceof \Laminas\Form\LabelAwareInterface) || !$oElement->getLabelOption('disable_html_escape')) {
                    $sLabel = $this->getEscapeHtmlHelper()->__invoke($sLabel);
                }

                switch ($this->getLabelPosition()) {
                    case self::LABEL_PREPEND:
                        $sOptionMarkup = sprintf($oLabelHelper->openTag($aLabelAttributes) . '%s ' . $oLabelHelper->closeTag() . '%s', $sLabel, $sOptionMarkup);
                        break;
                    case self::LABEL_APPEND:
                    default:
                        $sOptionMarkup = sprintf($oLabelHelper->openTag($aLabelAttributes) . '%s' . $oLabelHelper->closeTag() . '%s', $sLabel, $sOptionMarkup);
                        break;
                }
            }
            $sMarkup .= ($sMarkup ? $this->getSeparator() : '') . $sOptionMarkup;
        }
        return $sMarkup;
    }*/
    
    public function renderOptions(
        MultiCheckbox $element,
        array $options,
        array $selectedOptions,
        array $attributes
    ): string {
        $escapeHtmlHelper = $this->getEscapeHtmlHelper();
        $labelHelper      = $this->getLabelHelper();
        $labelClose       = $labelHelper->closeTag();
        $labelPosition    = $this->getLabelPosition();
        $globalLabelAttributes = [];
        $closingBracket   = $this->getInlineClosingBracket();

        if ($element instanceof LabelAwareInterface) {
            $globalLabelAttributes = $element->getLabelAttributes();
        }

        if ($globalLabelAttributes === []) {
            $globalLabelAttributes = $this->labelAttributes;
        }

        $combinedMarkup = [];
        $count          = 0;

        foreach ($options as $key => $optionSpec) {
            $count++;
            if ($count > 1 && array_key_exists('id', $attributes)) {
                unset($attributes['id']);
            }

            $value           = '';
            $label           = '';
            $inputAttributes = $attributes;
            $labelAttributes = $globalLabelAttributes;
            $selected        = (isset($inputAttributes['selected'])
                && $inputAttributes['type'] != 'radio'
                && $inputAttributes['selected']);
            $disabled        = (isset($inputAttributes['disabled']) && $inputAttributes['disabled']);

            if (is_scalar($optionSpec)) {
                $optionSpec = [
                    'label' => $optionSpec,
                    'value' => $key
                ];
            }

            if (isset($optionSpec['value'])) {
                $value = $optionSpec['value'];
            }
            if (isset($optionSpec['label'])) {
                $label = $optionSpec['label'];
            }
            if (isset($optionSpec['selected'])) {
                $selected = $optionSpec['selected'];
            }
            if (isset($optionSpec['disabled'])) {
                $disabled = $optionSpec['disabled'];
            }
            if (isset($optionSpec['label_attributes'])) {
                $labelAttributes = (isset($labelAttributes))
                    ? array_merge($labelAttributes, $optionSpec['label_attributes'])
                    : $optionSpec['label_attributes'];
            }
            if (isset($optionSpec['attributes'])) {
                $inputAttributes = array_merge($inputAttributes, $optionSpec['attributes']);
            }

            if (in_array($value, $selectedOptions)) {
                $selected = true;
            }

            $inputAttributes['value']    = $value;
            $inputAttributes['checked']  = $selected;
            $inputAttributes['disabled'] = $disabled;
            if (!isset($inputAttributes['id'])) {
                $inputAttributes['id'] = trim((string) $label) . '_' . substr(md5((string) $label), 0, 4);
            }
            $labelAttributes['for'] = $inputAttributes['id'];
            $input = sprintf(
                '<input %s%s',
                $this->createAttributesString($inputAttributes),
                $closingBracket
            );

            if (null !== ($translator = $this->getTranslator())) {
                $label = $translator->translate(
                    $label,
                    $this->getTranslatorTextDomain()
                );
            }

            if (! $element instanceof LabelAwareInterface || ! $element->getLabelOption('disable_html_escape')) {
                $label = $escapeHtmlHelper($label);
            }

            $labelOpen = $labelHelper->openTag($labelAttributes);
            $template  = '%s' . $labelOpen . '%s' . $labelClose;
            switch ($labelPosition) {
                case self::LABEL_PREPEND:
                    $markup = sprintf($template, $label, $input);
                    break;
                case self::LABEL_APPEND:
                default:
                    $markup = sprintf($template, $input, $label);
                    break;
            }

            $combinedMarkup[] = $markup;
        }

        return implode($this->getSeparator(), $combinedMarkup);
    }
}
<?php

namespace AreaRestrita\Form\View\Helper;

use InvalidArgumentException;
use LogicException;
use \Laminas\Form\View\Helper\FormMultiCheckbox as ZFFormMultiCheckbox;
use Laminas\Form\ElementInterface;
use Laminas\Form\Element\MultiCheckbox as MultiCheckboxElement;
use Laminas\Form\View\Helper\FormRow;

class FormMultiCheckbox extends ZFFormMultiCheckbox
{
    public function _render(ElementInterface $oElement): string
    {
        $aElementOptions = $oElement->getOptions();
        // For inline multi-checkbox
        if (isset($aElementOptions['inline']) && $aElementOptions['inline'] == true) {
            $this->setSeparator('');
            $oElement->setLabelAttributes(['class' => 'checkbox-inline']);

            return parent::render($oElement);
        }

        $this->setSeparator('</div><div class="checkbox">');
        $oElement->setLabelAttributes(['class' => 'checkbox']);

        return sprintf('<div class="checkbox">%s</div>', parent::render($oElement));
    }

    public function _renderOptions(
        MultiCheckboxElement $element,
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

        natsort($options);

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

            if (($translator = $this->getTranslator()) instanceof \Laminas\I18n\Translator\TranslatorInterface) {
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

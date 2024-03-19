<?php

namespace AreaRestrita\Form\View\Helper;

use Laminas\Form\Element\Checkbox as CheckboxElement;
use Laminas\Form\ElementInterface;
use Laminas\Form\Exception;
use Laminas\Form\View\Helper\FormLabel;

class FormCheckbox extends \Laminas\Form\View\Helper\FormCheckbox
{
    public function render(ElementInterface $element): string
    {
        if ($element->getOption('disable-twb')) {
            return parent::render($element);
        }

        if (!$element instanceof CheckboxElement) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s requires that the element is of type Laminas\Form\Element\Checkbox',
                __METHOD__
            ));
        }

        $name = $element->getName();
        if ($name === null || $name === '') {
            throw new Exception\DomainException(sprintf(
                '%s requires that the element has an assigned name; none discovered',
                __METHOD__
            ));
        }

        $attributes = $element->getAttributes();

        if ($element->getAttribute('id') === null) {
            $attributes['id'] = $name . '_' . substr(md5($name), 0, 4);
            $element->setAttribute('id', $attributes['id']);
        }

        $attributes['name'] = $name;
        $attributes['type'] = $this->getInputType();
        $attributes['value'] = $element->getCheckedValue();

        $closingBracket = $this->getInlineClosingBracket();

        if ($element->isChecked()) {
            $attributes['checked'] = 'checked';
        }

        $rendered = sprintf(
            '<input %s%s',
            $this->createAttributesString($attributes),
            $closingBracket
        );

        if ($element->useHiddenElement()) {
            $hiddenAttributes = [
                'disabled' => $attributes['disabled'] ?? false,
                'name' => $attributes['name'],
                'value' => $element->getUncheckedValue(),
            ];

            $rendered = sprintf(
                '<input type="hidden" %s%s',
                $this->createAttributesString($hiddenAttributes),
                $closingBracket
            ) . $rendered;
        }

        $label = $element->getLabel();
        if ($label) {
            $formLabel = new FormLabel();
            $rendered .= $formLabel($element);
        }

        return $rendered;
    }
}

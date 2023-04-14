<?php

namespace AreaRestrita\Form\View\Helper;

use InvalidArgumentException;
use LogicException;
use TwbBundle\Form\View\Helper\TwbBundleFormCheckbox;
use Zend\Form\ElementInterface;
use Zend\Form\Element\Checkbox;
use Zend\Form\View\Helper\FormRow;

class FormCheckbox extends TwbBundleFormCheckbox
{

    public function render(ElementInterface $oElement): string
    {
        if ($oElement->getOption('disable-twb')) {
            return parent::render($oElement);
        }

        if (!$oElement instanceof Checkbox) {
            throw new InvalidArgumentException(sprintf(
                    '%s requires that the element is of type Zend\Form\Element\Checkbox',
                    __METHOD__
            ));
        }
        if (($sName = $oElement->getName()) !== 0 && empty($sName)) {
            throw new LogicException(sprintf(
                    '%s requires that the element has an assigned name; none discovered',
                    __METHOD__
            ));
        }

        $aAttributes = $oElement->getAttributes();
        $aAttributes['name'] = $sName;
        $aAttributes['type'] = $this->getInputType();
        $aAttributes['value'] = $oElement->getCheckedValue();
        if (!isset($aAttributes['id'])) {
            $aAttributes['id'] = $sName . '_' . substr(md5($sName), 0, 4);
        }
        $sClosingBracket = $this->getInlineClosingBracket();

        if ($oElement->isChecked()) {
            $aAttributes['checked'] = 'checked';
        }

        // Render label
        $sLabelOpen = $sLabelClose = '';
        $sLabelContent = $this->getLabelContent($oElement);
        if ($sLabelContent) {
            $labelAttributes = $oElement->getLabelAttributes();
            $labelAttributes['for'] = $aAttributes['id'];
            $oElement->setLabelAttributes($labelAttributes);

            $oLabelHelper = $this->getLabelHelper();
            $sLabelOpen = $oLabelHelper->openTag($oElement->getLabelAttributes() ?: null);
            $sLabelClose = $oLabelHelper->closeTag();
        }

        // Render checkbox
        $sElementContent = sprintf('<input %s%s', $this->createAttributesString($aAttributes), $sClosingBracket);

        // Add label markup
        if ($this->getLabelPosition($oElement) === FormRow::LABEL_PREPEND) {
            $sElementContent = $sElementContent .
                $sLabelOpen .
                ($sLabelContent ? rtrim($sLabelContent) . ' ' : '') .
                $sLabelClose;
        } else {
            $sElementContent = $sElementContent .
                $sLabelOpen .
                ($sLabelContent ? ' ' . ltrim($sLabelContent) : '') .
                $sLabelClose;
        }

        //Render hidden input
        if ($oElement->useHiddenElement()) {
            $sElementContent = sprintf(
                    '<input type="hidden" %s%s',
                    $this->createAttributesString(['name' => $aAttributes['name'], 'value' => $oElement->getUncheckedValue()]),
                    $sClosingBracket
                ) . $sElementContent;
        }

        return $sElementContent;
    }
}

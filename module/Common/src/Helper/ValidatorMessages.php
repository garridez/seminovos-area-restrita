<?php

namespace SnBH\Common\Helper;

use Laminas\Form\Form;

/**
 * Converte a resposta dos validators em alguma coisa
 */
class ValidatorMessages
{
    /**
     * @param array $messages
     * @param string $format
     * @return array
     */
    public static function includeLabelToMessage($messages, Form $form, $format = '{label}: {message}')
    {
        foreach ($messages as $name => $messagesArray) {
            if (!$form->has($name)) {
                continue;
            }
            $input = $form->get($name);
            $label = $input->getLabel();
            foreach ($messagesArray as $k => $message) {
                $messages[$name][$k] = str_replace(['{label}', '{message}'], [$label, $message], $format);
            }
        }
        return $messages;
    }

    /**
     * @param array $messages
     * @return string
     */
    public static function toHTML($messages, ?Form $form = null)
    {
        if ($form) {
            $messages = self::includeLabelToMessage($messages, $form, '<b>{label}</b>: <i>{message}</i>');
        }
        $html = '';
        $divider = "<br />\n";
        foreach ($messages as $messagesArray) {
            $html .= implode($divider, $messagesArray) . $divider;
        }
        return $html;
    }
}

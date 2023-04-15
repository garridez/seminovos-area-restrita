<?php

namespace AreaRestrita\Log\Processors;

use Laminas\Log\Processor\ProcessorInterface;

class UserRequest implements ProcessorInterface
{

    public function process(array $event)
    {
        $userRequest = [
            'session_id' => null,
            'REQUEST_URI' => $_SERVER['REQUEST_URI'],
            'QUERY_STRING' => $_SERVER['QUERY_STRING'],
            'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'],
            'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'],
            'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'],
            'REMOTE_PORT' => $_SERVER['REMOTE_PORT'],
            'REQUEST_TIME_FLOAT' => $_SERVER['REQUEST_TIME_FLOAT'],
            '_FILES' => $_FILES ?? false,
            '_POST' => $_POST ?? false,
            '_GET' => $_GET ?? false,
            '_SESSION' => $_SESSION ?? false,
        ];
        if (session_status() === PHP_SESSION_ACTIVE) {
            $userRequest['session_id'] = session_id();
        }


        $event['extra']['UserRequest'] = $userRequest;

        return $event;
    }
}

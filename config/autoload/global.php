<?php

/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
use Zend\Cache\Storage\Adapter\Redis;
use Zend\Session\Storage\SessionArrayStorage;

return [
    'SnBH' => [
        'urls' => [
            'site' => getenv('SNBH_URL_SITE')
        ]
    ],
    'ApiClient' => [
        'credentials' => [
            'serverUrl' => 'http://54.200.165.200',
            /**
             * @todo Remover quando resolver o problema de rede
             */
            'serverUrl' => (function() {
                    $ctx = stream_context_create(array('http' =>
                        array(
                            'timeout' => 1,
                    )));
                    $apiUrls = [
                        0 => 'http://api2.seminovosbh.com.br',
                        1 => 'http://54.200.165.200'
                    ];
                    $apiUrlsKey = rand(0, 1);
                    $serverUrl = $apiUrls[$apiUrlsKey];

                    if (!@file_get_contents($serverUrl, false, $ctx)) {
                        unset($apiUrls[$apiUrlsKey]);
                        reset($apiUrls);
                        $serverUrl = current($apiUrls);
                    }
                    return $serverUrl;
                })(),
            'headers' => [
                'Accept' => 'application/vnd.seminovos-bh.v1+json'
            ],
            'options' => [
                'timeout' => 60 * 3
            ]
        ],
        'cache' => [
            'use_from_service_manager' => 'cache',
        ],
    ],
    'UsersModuleApi' => [
        'base_uri' => 'http://microservice-users.seminovos.com.br',
        'support' => [
            'user' => 'micro_users',
            'password' => 'microusersuibd8syhd8',
        ],
    ],
    'log' => [
        'logger' => [
            'writers' => [
                's3' => [
                    'name' => \AreaRestrita\Log\Writer\S3::class,
                    'options' => [
                        'formatter' => \Zend\Log\Formatter\Json::class,
                    ],
                ],
            ],
            'processors' => [
                'Backtrace' => [
                    'name' => Zend\Log\Processor\Backtrace::class,
                ],
                'UserRequest' => [
                    'name' => \AreaRestrita\Log\Processors\UserRequest::class,
                ],
            ],
        ],
    ],
    'dir' => [
        'temp' => 'data/temp',
        'upload' => 'data/temp/upload'
    ],
    'cache' => array(
        'adapter' => Redis::class,
        'options' => array(
            'server' => 'tcp://session.ugt1op.ng.0001.usw2.cache.amazonaws.com:6379?weight=1&timeout=1',
            'ttl' => 300, # 5 Minutos
            'namespace' => 'AreaRestritaProd'
        ),
        'plugins' => array(
            'Serializer',
        )
    ),
    'session_config' => [
        'cookie_lifetime' => 60 * 60 * 24 * 31 * 12,
        'gc_maxlifetime' => 60 * 60 * 24 * 5,
    ],
    'session_manager' => [
        'validators' => [
        ],
        'options' => [
            'attach_default_validators' => false
        ],
    ],
    'session_storage' => [
        'type' => SessionArrayStorage::class
    ],
];

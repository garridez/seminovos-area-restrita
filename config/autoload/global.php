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

use Laminas\Cache\Storage\Adapter\Redis;
use Laminas\Session\Storage\SessionArrayStorage;

return [
    'SnBH' => [
        'urls' => [
            'site' => getenv('SNBH_URL_SITE')
        ]
    ],
    'ApiClient' => [
        'credentials' => [
            'serverUrl' => 'http://api2.seminovos.com.br',
            // 'serverUrl' => 'http://snbh-api',
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
    'log' => [
        'logger' => [
            'writers' => [
                's3' => [
                    'name' => \AreaRestrita\Log\Writer\S3::class,
                    'options' => [
                        'formatter' => \Laminas\Log\Formatter\Json::class,
                    ],
                ],
            ],
            'processors' => [
                'Backtrace' => [
                    'name' => Laminas\Log\Processor\Backtrace::class,
                ],
                'UserRequest' => [
                    'name' => \AreaRestrita\Log\Processors\UserRequest::class,
                ],
                'ApplicationNamespace' => [
                    'name' => \AreaRestrita\Log\Processors\ApplicationNamespace::class,
                ],
            ],
        ],
    ],
    'dir' => [
        'temp' => 'data/temp',
        'upload' => 'data/temp/upload'
    ],
    'caches' => [
        'cache' => [
            'adapter' => Redis::class,
            'options' => [
                'server' => 'tcp://session.ugt1op.ng.0001.usw2.cache.amazonaws.com:6379?weight=1&timeout=1',
                'ttl' => 300, # 5 Minutos
                'namespace' => 'AreaRestritaProd'
            ],
            'plugins' => [
                [
                    'name' => 'Serializer',
                ]
            ]
        ],
    ],
    'session_config' => [
        'cookie_lifetime' => 60 * 60 * 24 * 31 * 12,
        'gc_maxlifetime' => 60 * 60 * 24 * 5,
    ],
    'session_manager' => [
        'validators' => [
            Laminas\Session\Validator\RemoteAddr::class,
            Laminas\Session\Validator\HttpUserAgent::class,
        ],
        'options' => [ ],
    ],
    'session_storage' => [
        'type' => SessionArrayStorage::class
    ],
];

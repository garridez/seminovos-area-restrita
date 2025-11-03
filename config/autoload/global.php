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

use Laminas\Session\Storage\SessionArrayStorage;

return [
    'SnBH' => [
        'urls' => [
            'site' => getenv('SNBH_URL_SITE'),
        ],
    ],
    'ApiClient' => [
        'credentials' => [
            //'serverUrl' => 'http://ip-10-0-1-196.us-west-2.compute.internal',
            'serverUrl' => 'http://api2.seminovos.com.br',
            // 'serverUrl' => 'http://snbh-api',
            'headers' => [
                'Accept' => 'application/vnd.seminovos-bh.v1+json',
            ],
            'options' => [
                'timeout' => 60 * 3,
            ],
        ],
        'cache' => [
            'use_from_service_manager' => 'cache',
        ],
    ],
    'log' => [
        'logger' => [
            'writers' => [
                'NewRelic' => [
                    'name' => SnBH\Logger\Writer\NewRelicWriter::class,
                ],
            ],
            'processors' => [],
        ],
    ],
    'dir' => [
        'temp' => 'data/temp',
        'upload' => 'data/temp/upload',
    ],
    'caches' => [
        'cache' => [
            'adapter' => Laminas\Cache\Storage\Adapter\Filesystem::class,
            'options' => [
                'ttl' => 300, // 5 Minutos
                'cacheDir' => 'data/cache',
                'namespace' => 'area-restrita',
            ],
            'plugins' => [
                [
                    'name' => 'Serializer',
                ],
            ],
        ],
    ],
    'session_config' => [],
    'session_manager' => [
        'validators' => [
            //Laminas\Session\Validator\RemoteAddr::class,
            Laminas\Session\Validator\HttpUserAgent::class,
        ],
        'options' => [],
    ],
    'session_storage' => [
        'type' => SessionArrayStorage::class,
    ],
];

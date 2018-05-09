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
use Zend\Cache\Storage\Adapter\Filesystem;
use Zend\Session\Storage\SessionArrayStorage;
use Zend\Session\Validator\HttpUserAgent;
use Zend\Session\Validator\RemoteAddr;

return [
    'ApiClient' => [
        'credentials' => [
            'serverUrl' => 'http://api2.seminovosbh.com.br',
            'headers' => [
                'Accept' => 'application/vnd.seminovos-bh.v1+json'
            ],
            'options' => [
                'timeout' => 30
            ]
        ],
    ],
    'cache' => array(
        'adapter' => Filesystem::class,
        'options' => array(
            'ttl' => 3600,
            'cacheDir' => 'data/cache',
        ),
        'plugins' => array(
            'Serializer',
        )
    ),
    'session_config' => [
        'cookie_lifetime' => 60 * 60 * 4,
        'gc_maxlifetime' => 60 * 60 * 24 * 30,
    ],
    'session_manager' => [
        'validators' => [
            RemoteAddr::class,
            HttpUserAgent::class,
        ]
    ],
    // Session storage configuration.
    'session_storage' => [
        'type' => SessionArrayStorage::class
    ],
    // ...
];

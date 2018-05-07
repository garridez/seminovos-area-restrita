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
        'adapter' => Zend\Cache\Storage\Adapter\Filesystem::class,
        'options' => array(
            'ttl' => 3600,
            'cacheDir' => 'data/cache',
        ),
        'plugins' => array(
            'Serializer',
        )
    )
];

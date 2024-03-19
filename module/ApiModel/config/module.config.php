<?php

namespace SnBH\ApiModel;

return [
    'service_manager' => [
        'abstract_factories' => [
            Model\ModelAbstractFactory::class,
        ],
    ],
    'SnBH\ApiModel' => [
        /**
         * Put here the namespaces to 'ModelAbstractFactory' create
         */
        'model_factory_namespace_prefix' => [
            'SnBH\ApiModel\Model',
        ],
    ],
];

<?php
/**
 * Constantes para facilitar o acesso aos dados
 */
define('APPLICATION_VERSION', file_get_contents('version'));
define('APPLICATION_ENV', getenv('APPLICATION_ENV'));

// Atalho semantico
define('IS_DEV', APPLICATION_ENV == 'development');
define('IS_PROD', !IS_DEV);

define('SNBH_API_HOST', getenv('SNBH_API_HOST'));
define('SNBH_URL_SITE', getenv('SNBH_URL_SITE'));

//chave webhook galayPay
define('WEBHOOK_GALAYPAT', '83ca263c5f7f8f9b57feaaf231f90b54');

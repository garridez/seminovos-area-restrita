<?php
/**
 * Constantes para facilitar o acesso aos dados
 */
define('APPLICATION_ENV', getenv('APPLICATION_ENV'));

// Atalho semantico
define('IS_DEV', APPLICATION_ENV == 'development');
define('IS_PROD', !IS_DEV);

define('SNBH_API_HOST', getenv('SNBH_API_HOST'));
define('SNBH_URL_SITE', getenv('SNBH_URL_SITE'));

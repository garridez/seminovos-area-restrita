<?php

namespace AreaRestrita\Service;

use AreaRestrita\Service;
use SnBH\ApiClient\Client as ApiClient;

class Identity
{

    protected $identity = false;
    protected $apiclient;

    public function __construct($identity, ApiClient $apiClient)
    {
        $this->identity = $identity;
        $this->apiclient = $apiClient;
    }

    public function hasIdentity()
    {
        return (bool) $this->identity;
    }

    public function getIdentity()
    {
        return $this->identity;
    }
}

<?php

namespace AreaRestrita\Service;

use AreaRestrita\Service;
use SnBH\ApiClient\Client as ApiClient;

class Identity
{

    protected $apiclient;

    public function __construct(protected $identity, ApiClient $apiClient)
    {
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

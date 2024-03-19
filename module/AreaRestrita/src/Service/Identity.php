<?php

namespace AreaRestrita\Service;

use SnBH\ApiClient\Client as ApiClient;

class Identity
{
    public function __construct(protected mixed $identity, protected ApiClient $apiClient)
    {
    }

    public function hasIdentity(): bool
    {
        return (bool) $this->identity;
    }

    public function getIdentity(): mixed
    {
        return $this->identity;
    }
}

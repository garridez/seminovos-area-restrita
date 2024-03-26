<?php

namespace SnBH\ApiModel\Model;

use BadMethodCallException;
use Laminas\ServiceManager\ServiceManager;
use SnBH\ApiClient\Client as ApiClient;
use SnBH\ApiClient\Response;

/**
 * @method Response get(array $where = array(), int $id = null, boolean $cacheable = false) Realiza get no endpoint correspondente à class
 * @method array    get(array $where = array(), int $id = null, boolean $cacheable = false) Realiza get no endpoint correspondente à class
 * @method Response post(array $data, int $id = null, boolean $cacheable = false) Realiza post no endpoint correspondente à class
 * @method array    post(array $data, int $id = null, boolean $cacheable = false) Realiza post no endpoint correspondente à class
 * @method Response put(array $data, int $id = null, boolean $cacheable = false) Realiza put no endpoint correspondente à class
 * @method array    put(array $data, int $id = null, boolean $cacheable = false) Realiza put no endpoint correspondente à class
 * @method Response delete(null $paramNull, int $id, boolean $cacheable = false) Realiza delete no endpoint correspondente à class
 * @method array    delete(null $paramNull, int $id, boolean $cacheable = false) Realiza delete no endpoint correspondente à class
 * @method Response patch(array $data, int $id = null, boolean $cacheable = false) Realiza patch no endpoint correspondente à class
 * @method array    patch(array $data, int $id = null, boolean $cacheable = false) Realiza patch no endpoint correspondente à class
 */
abstract class AbstractModel
{
    protected array $allowedHttpMethods = [
        'get' => 'Get',
        'post' => 'Post',
        'put' => 'Put',
        'head' => 'Head',
        'delete' => 'Delete',
        'patch' => 'Patch',
        'options' => 'Options',
        'trace' => 'Trace',
        'connect' => 'Connect',
        'propfind' => 'Propfind',
    ];

    public function __construct(protected ApiClient $apiClient, protected ServiceManager $container)
    {
    }

    /**
     * @return void
     */
    protected function doRequest()
    {
    }

    /**
     * @param string $name
     * @param array $arguments
     */
    public function __call($name, $arguments)
    {
        if (!isset($this->allowedHttpMethods[$name])) {
            throw new BadMethodCallException("The http method \"{$name}\" not allowed");
        }

        $method = $this->getEndPoint() . $this->allowedHttpMethods[$name];

        return call_user_func_array([$this->apiClient, $method], $arguments);
    }

    protected function getEndPoint(): string
    {
        $endPoint = explode('\\', static::class);
        return lcfirst(end($endPoint));
    }
}

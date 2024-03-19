<?php

namespace SnBH\ApiModel\Model;

use AreaRestrita\Model\Traits\TraitIdentity;
use Laminas\Cache\Storage\Adapter\Filesystem;

class Veiculos extends AbstractModel
{
    use TraitIdentity;

    protected function getCacheKey(): string
    {
        return 'isOwner_cache_' . $this->getIdentity() . md5(__METHOD__ . 'Cache');
    }

    /**
     * Essa função verifica se o idVeiculo pertence ao idCadastro que está loggado
     *
     * @param int|array $idVeiculo Pode ser um id ou um array de id
     */
    public function isOwner(int|array $idVeiculo)
    {
        $idCadastro = $this->getIdentity();

        /** @var Filesystem $cache */
        $cache = $this->container->get('cache');
        $cacheOptions = $cache->getOptions();
        $ttlOriginal = $cacheOptions->getTtl();
        $cacheOptions->setTtl(5);
        $cacheKey = $this->getCacheKey();

        $idVeiculo = (array) $idVeiculo;
        $idVeiculo = array_map('intVal', $idVeiculo);

        $veiculos = $cache->getItem($cacheKey);
        if ($veiculos === null) {
            $veiculos = $this->get([
                'idCadastro' => $idCadastro,
                'fastMode' => 1,
                'fields' => ['idVeiculo'],
            ])->getData();
            $veiculos = array_map(function ($item): int {
                return (int) $item['idVeiculo'];
            }, $veiculos);
            $cache->setItem($cacheKey, $veiculos);
        }
        $cacheOptions->setTtl($ttlOriginal);
        /**
         * Retorna true se todos os id de um continver no outro
         */
        return count(array_intersect($idVeiculo, $veiculos)) === count($idVeiculo);
    }

    public function clearIsOwnerCache()
    {
        $cacheKey = $this->getCacheKey();

        /** @var Filesystem $cache */
        $cache = $this->container->get('cache');

        return $cache->removeItem($cacheKey);
    }
}

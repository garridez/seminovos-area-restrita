<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestrita\Controller;

class CheckController extends AbstractActionController
{

    public function indexAction(): never
    {
        $this->incrementSubversion();

        $data = [
            'status' => 'ok',
            'aplication_time' => round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 2),
            'version' => file_get_contents('version')
        ];
        die(json_encode($data));
    }

    /**
     * Incrementa a subversão do sistema a cada 2 minutos até chegar na subversão 5
     * 
     * Isso é para sincronizar os dados dos vários servidores no autoscaling
     * 
     */
    protected function incrementSubversion()
    {
        $version = file_get_contents('version');

        preg_match('/(?<version>^[0-9]+\.[0-9]+\.[0-9]+)(?<subVersion>.*$)/', $version, $matches);
        $version = $matches['version'];
        $subVersion = trim($matches['subVersion'], '-') ?: '0';

        if (is_numeric($subVersion) && $subVersion < 5) {
            // Tem que o arquivo foi modificado em minutos
            $versionMTime = (time() - filemtime('version')) / 60;
            if ($versionMTime > 2) {
                $subVersion++;
                file_put_contents('version', $version . '-' . $subVersion);
            }
        } else if (is_numeric($subVersion) && $subVersion >= 5) {
            file_put_contents('version', $version . '-' . 'a');
        }
    }
}

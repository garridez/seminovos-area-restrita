<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace AreaRestrita\Controller;

class CheckController extends AbstractActionController
{

    public function indexAction()
    {
        $data = [
            'status' => 'ok',
            'aplication_time' => round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 2),
            'version' => file_get_contents('version')
        ];
        die(json_encode($data));
    }
}

<?php

namespace ShoppingCart;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

use ShoppingCart\Model\ShoppingCartTable;
use ShoppingCart\Model\ShoppingCart;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'ShoppingCart\Model\ShoppingCartTable' => function($sm) {
                    $tableGateway = $sm->get('ShoppingCartGateway');
                    $table = new ShoppingCartTable($tableGateway);
                    return $table;
                },
                'ShoppingCartGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new ShoppingCart());
                    return new TableGateway('shopping_cart', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }

}

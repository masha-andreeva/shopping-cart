<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'ShoppingCart\Controller\ShoppingCart' => 'ShoppingCart\Controller\ShoppingCartController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'shoppingCart' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/shoppingCart[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z][a-zA-Z0-9_-]+',
                    ),
                    'defaults' => array(
                        'controller' => 'ShoppingCart\Controller\ShoppingCart',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'shoppingCart' => __DIR__ . '/../view',
        ),
    ),
);

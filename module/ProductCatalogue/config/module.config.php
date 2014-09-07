<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'ProductCatalogue\Controller\Product' => 'ProductCatalogue\Controller\ProductController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'product' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/product[/][:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'ProductCatalogue\Controller\Product',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'product' => __DIR__ . '/../view',
        ),
    ),
);

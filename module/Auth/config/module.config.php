<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'auth' => 'Auth\Controller\AuthController',
        ),
    ),
    // start route
    'router' => array(
        'routes' => array(
            'auth' => array(
                'type' => 'Literal',
                'priority' => 1000,
                'options' => array(
                    'route' => '/auth',
                    'defaults' => array(
                        'controller' => 'auth',
                        'action' => 'login',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'login' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/login',
                            'defaults' => array(
                                'controller' => 'auth',
                                'action' => 'login',
                            ),
                        ),
                    ),
                    'authenticate' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/authenticate',
                            'defaults' => array(
                                'controller' => 'auth',
                                'action' => 'authenticate',
                            ),
                        ),
                    ),
                    'logout' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/logout',
                            'defaults' => array(
                                'controller' => 'auth',
                                'action' => 'logout',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    // end route
    'view_manager' => array(
        'template_path_stack' => array(
            'auth' => __DIR__ . '/../view',
        ),
    ),
    
);
<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'AdminUser\Controller\AdminUser' => 'AdminUser\Controller\AdminUserController',            
        ),
    ),
    
    'router' => array(
        'routes' => array(
            'admin-user' => array(
                'type'    => 'segment',
                'options' => array(
                    'route' => '/admin-user[/:action][/:uid]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'AdminUser\Controller\AdminUser',
                        'action'     => 'index',
                    ),
                ),
            ),
        ),
    ),    
    
    'view_manager' => array(
        'template_path_stack' => array(
            'admin-user' => __DIR__ . '/../view',
        ),
    ),
);
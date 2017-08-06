<?php
return array(
    'controllers' => array(
        'invokables' => array(            
            'User\Controller\UserRest' => 'User\Controller\UserRestController',
            'User\Controller\FollowRest' => 'User\Controller\FollowRestController',
            'User\Controller\AudienceRest' => 'User\Controller\AudienceRestController',
            'User\Controller\HomeRest' => 'User\Controller\HomeRestController',
            'User\Controller\CategoryRest' => 'User\Controller\CategoryRestController',
            'User\Controller\FacebookRest' => 'User\Controller\FacebookRestController',
            'User\Controller\ForgotPasswordRest' => 'User\Controller\ForgotPasswordRestController',
            'User\Controller\User' => 'User\Controller\UserController',
        ),
    ),         
    
    'router' => array(
        'routes' => array(
            'user-rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/user-rest[/:id][/:sub_route]',
                    'constraints' => array('id' => '[0-9]+', ),
                    'defaults' => array(
                        'controller' => 'User\Controller\UserRest',
                    ),
                 ),     
             ),
             'follow-rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/follow-rest[/:id]',
                    'constraints' => array('id' => '[0-9]+', ),
                    'defaults' => array(
                        'controller' => 'User\Controller\FollowRest',
                    ),
                 ),     
             ),
             'category-rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/category-rest[/:id]',
                    'constraints' => array('id' => '[0-9]+', ),
                    'defaults' => array(
                        'controller' => 'User\Controller\CategoryRest',
                    ),
                 ),     
             ),
             'audience-rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/audience-rest[/:id]',
                    'constraints' => array('id' => '[0-9]+', ),
                    'defaults' => array(
                        'controller' => 'User\Controller\AudienceRest',
                    ),
                 ),     
             ),
             'facebook-rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/facebook-rest[/:id]',
                    'constraints' => array('id' => '[0-9]+', ),
                    'defaults' => array(
                        'controller' => 'User\Controller\FacebookRest',
                    ),
                 ),     
             ),
             'home-rest' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/home-rest',
                    'defaults' => array(
                        'controller' => 'User\Controller\HomeRest',
                    ),
                ),
             ),
             'forgot-password-rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/forgot-password-rest[/:id]',
                    'constraints' => array('id' => '[0-9]+', ),
                    'defaults' => array(
                        'controller' => 'User\Controller\ForgotPasswordRest',
                    ),
                 ),     
             ),
             'user_avatar' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/user/avatar_image/:file',
                ),
             ),
             'user_cover_image' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/user/cover_image/:file',
                ),
             ),
             'category_image' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/category/image/:file',
                ),
             ),
             'category_icon' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/category/icon/:file',
                ),
             ),
             'search' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/search',
                ),
                'may_terminate' => true,
                'child_routes' => array(                    
                    'user' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/user',
                            'defaults' => array(
                                'controller' => 'User\Controller\UserRest',
                                'action'     => 'search',
                            ),
                        ),
                    ),
                ),
             ),
             'user' => array(
                'type'    => 'segment',
                'options' => array(
                    'route' => '/user-app[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'User\Controller\User',
                        'action'     => 'index',
                    ),
                ),
             ),   
         ),
     ),
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
        'template_path_stack' => array(
            'user' => __DIR__ . '/../view',
        ),
    ),
);    
<?php
return array(
    'controllers' => array(
        'invokables' => array(            
            'Sound\Controller\LikeRest' => 'Sound\Controller\LikeRestController',
            'Sound\Controller\ViewRest' => 'Sound\Controller\ViewRestController',
            'Sound\Controller\PlayRest' => 'Sound\Controller\PlayRestController',
            'Sound\Controller\CommentRest' => 'Sound\Controller\CommentRestController',
            'Sound\Controller\SoundRest' => 'Sound\Controller\SoundRestController',
            'Sound\Controller\FavoriteRest' => 'Sound\Controller\FavoriteRestController',
            'Sound\Controller\Sound' => 'Sound\Controller\SoundController',
            'Sound\Controller\Comment' => 'Sound\Controller\CommentController',
        ),
    ),    
    'controller_plugins' => array(
        'invokables' => array(
            'TimeAgo' => 'Sound\Controller\Plugin\TimeAgo',
        )
    ),       
    
    'router' => array(
        'routes' => array(
            'like-rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/like-rest[/:id]',
                    'constraints' => array('id' => '[0-9]+', ),
                    'defaults' => array(
                        'controller' => 'Sound\Controller\LikeRest',
                    ),
                 ),     
             ),
             'view-rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/view-rest[/:id]',
                    'constraints' => array('id' => '[0-9]+', ),
                    'defaults' => array(
                        'controller' => 'Sound\Controller\ViewRest',
                    ),
                 ),     
             ),
             'play-rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/play-rest[/:id]',
                    'constraints' => array('id' => '[0-9]+', ),
                    'defaults' => array(
                        'controller' => 'Sound\Controller\PlayRest',
                    ),
                 ),     
             ),       
             'comment-rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/comment-rest[/:id]',
                    'constraints' => array('id' => '[0-9]+', ),
                    'defaults' => array(
                        'controller' => 'Sound\Controller\CommentRest',
                    ),
                 ),     
             ),
             'sound-rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/sound-rest[/:id][/:sub_route]',
                    'constraints' => array('id' => '[0-9]+', ),
                    'defaults' => array(
                        'controller' => 'Sound\Controller\SoundRest',
                    ),
                 ),
             ),             
             'favorite-rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/favorite-rest[/:id]',
                    'constraints' => array('id' => '[0-9]+', ),
                    'defaults' => array(
                        'controller' => 'Sound\Controller\FavoriteRest',
                    ),
                 ),     
             ),             
             'sound_thumbnail' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/sound/thumbnail/:file',
                ),
             ),
             'sound_path' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/sound/sound_file/:file',
                ),
             ),
             'search' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/search',
                ),
                'may_terminate' => true,
                'child_routes' => array(                    
                    'sound' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/sound',
                            'defaults' => array(
                                'controller' => 'Sound\Controller\SoundRest',
                                'action'     => 'search',
                            ),
                        ),
                    ),
                    'tag' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/tag',
                            'defaults' => array(
                                'controller' => 'Sound\Controller\SoundRest',
                                'action'     => 'searchByTag',
                            ),
                        ),
                    ),
                    'sound-tag' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/sound-tag',
                            'defaults' => array(
                                'controller' => 'Sound\Controller\SoundRest',
                                'action'     => 'searchSoundByTag',
                            ),
                        ),
                    ),
                    'all' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/all',
                            'defaults' => array(
                                'controller' => 'Sound\Controller\SoundRest',
                                'action'     => 'searchAll',
                            ),
                        ),
                    ),
                ),
             ),
             'sound' => array(
                'type'    => 'segment',
                'options' => array(
                    'route' => '/sound-app[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Sound\Controller\Sound',
                        'action'     => 'index',
                    ),
                ),
             ),
             'comment' => array(
                'type'    => 'segment',
                'options' => array(
                    'route' => '/comment[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Sound\Controller\Comment',
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
            'sound' => __DIR__ . '/../view',
        ),
    ),
);    
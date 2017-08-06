<?php
return array(
    'controllers' => array(
        'invokables' => array(            
            'Settings\Controller\AppConfigRest' => 'Settings\Controller\AppConfigRestController',
            'Settings\Controller\SettingsRest' => 'Settings\Controller\SettingsRestController',
            'Settings\Controller\StoragePlanRest' => 'Settings\Controller\StoragePlanRestController',
            'Settings\Controller\AppConfig' => 'Settings\Controller\AppConfigController',
            'Settings\Controller\HelpCenter' => 'Settings\Controller\HelpCenterController',
            'Settings\Controller\DeviceTokenRest' => 'Settings\Controller\DeviceTokenRestController',
            'Settings\Controller\PushRest' => 'Settings\Controller\PushRestController',
            'Settings\Controller\NotificationRest' => 'Settings\Controller\NotificationRestController',
        ),
    ),         
    
    'router' => array(
        'routes' => array(
            'appconfig-rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/appconfig-rest[/:id]',
                    'constraints' => array('id' => '[0-9]+', ),
                    'defaults' => array(
                        'controller' => 'Settings\Controller\AppConfigRest',
                    ),
                 ),     
             ),
             'push-rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/push-rest[/:id]',
                    'constraints' => array('id' => '[0-9]+', ),
                    'defaults' => array(
                        'controller' => 'Settings\Controller\PushRest',
                    ),
                 ),     
             ),
             'notification-rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/notification-rest[/:id]',
                    'constraints' => array('id' => '[0-9]+', ),
                    'defaults' => array(
                        'controller' => 'Settings\Controller\NotificationRest',
                    ),
                 ),     
             ),
             'settings-rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/settings-rest[/:sub_route]',
                    'defaults' => array(
                        'controller' => 'Settings\Controller\SettingsRest',
                    ),
                 ),     
             ),
             'device-token-rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/device-token-rest[/:id]',
                    'defaults' => array(
                        'controller' => 'Settings\Controller\DeviceTokenRest',
                    ),
                 ),     
             ),
             'storage-plan-rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/storage-plan-rest',
                    'defaults' => array(
                        'controller' => 'Settings\Controller\StoragePlanRest',
                    ),
                 ),     
             ),
             'storage_plan_image' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/storage_plan/image/:file',
                ),
             ),
             'app-config' => array(
                'type'    => 'segment',
                'options' => array(
                    'route' => '/app-config[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Settings\Controller\AppConfig',
                        'action'     => 'index',
                    ),
                ),
             ),
             'help-center' => array(
                'type'    => 'segment',
                'options' => array(
                    'route' => '/help-center[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Settings\Controller\HelpCenter',
                        'action'     => 'index',
                    ),
                ),
             ),
             'email-image' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/img/:file',
                ),
             ),
         ),
     ),
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
        'template_path_stack' => array(
            'settings' => __DIR__ . '/../view',
        ),
    ),
);    
<?php

use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Authentication\AuthenticationService;
use Api\Authentication\Storage\OAuth2 as OAuth2Storage;
use Api\Authentication\Adapter\OAuth2 as OAuth2Adapter;

return array(
    'service_manager' => array(
        'factories' => array(
            'OAuth2\Server' => 'Api\Service\OAuth2ServerFactory',
            'Api\Authenticate\Adapter' => function (ServiceLocatorInterface $serviceLocator) {
                return new OAuth2Adapter($serviceLocator->get('OAuth2\Server'));
            },
            'Api\Authenticate\AuthenticationService' => function(ServiceLocatorInterface $serviceLocator) {
                $storage = new OAuth2Storage(
                    $serviceLocator->get('OAuth2\Server'),
                    $serviceLocator->get('User\Model\UserDao')
                );
                
                return new AuthenticationService($storage);
            }
        ),
        'aliases' => array(
            'Zend\Authentication\AuthenticationService' => 'Api\Authenticate\AuthenticationService',
        ),        
    ),
    'controllers' => array(
        'invokables' => array(
            'Api\Controller\AuthRest' => 'Api\Controller\AuthRestController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'auth-rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/auth-rest[/:action]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller'    => 'Api\Controller\AuthRest',
                        'action'        => 'login',
                    ),
                ),
            ),
        ),
    ),
);
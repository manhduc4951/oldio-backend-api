<?php
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\Authentication\AuthenticationService as AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;


return array (
    'factories' => array (        
        'user_authenticate_service' => function($sm) {
            $authService = new AuthenticationService();
            $authService->setAdapter($sm->get('Zend\Authentication\Adapter\DbTable'));
            return $authService;
        },
        'Zend\Authentication\Adapter\DbTable' => function($sm) {
            $dbTableAuthAdapter = new AuthAdapter($sm->get('Zend\Db\Adapter\Adapter'),
                    'admin_user',
                    'username',
                    'password',
                    'md5(?) AND status = 1'
            );

            return $dbTableAuthAdapter;
        },
    ),
);
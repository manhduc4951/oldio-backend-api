<?php

namespace Api\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Factory for OAuth2 server.
 * 
 * @package Api_Service
 * @author duyld
 */
class OAuth2ServerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');
        $storage = new \OAuth2_Storage_Pdo($config['db'], array('user_table' => 'user'));
        
        $server = new \OAuth2_Server($storage);
        $server->addGrantType(new \OAuth2_GrantType_UserCredentials($storage));
        $server->addGrantType(new \OAuth2_GrantType_RefreshToken($storage));
        
        return $server;
    }
}
<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
 
error_reporting(E_ALL);
ini_set('display_errors', true);
 
return array(
    'db' => array(
        'driver'         => 'Pdo',
        //'dsn'            => 'mysql:dbname=ica467db;host=ica467db.csd1bbfpxzwe.ap-southeast-1.rds.amazonaws.com',
        'dsn'            => 'mysql:dbname=ica467db;host=localhost',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
        'username' => 'mysql',
        'password' => 'mysql',
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',            
            'Zend\Cache\Storage\ZendServerShm' => function($sm){
                 $cache = Zend\Cache\StorageFactory::factory(array(
                     'adapter' => array(
                        'name' => 'filesystem',
                        'options' => array(
                            'cache_dir' => './data/cache/',
                            'key_pattern' => '/^[a-z0-9_\+\-.]*$/Di',
                        )
                     ),
                     'maxTtl' => '3600',
                     'plugins' => array(
                         'exception_handler' => array('throw_exceptions' => false),
                     )
                 ));
                
                 return $cache;
             },                                
        ),
    ),
);

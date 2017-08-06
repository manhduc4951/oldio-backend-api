<?php
namespace Sound;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(__dir__ . '/autoload_classmap.php', ),
            'Zend\Loader\StandardAutoloader' => array('namespaces' => array(__namespace__ =>
                        __dir__ . '/src/' . __namespace__, ), ),
            );
    }

    public function getConfig()
    {
        return include __dir__ . '/config/module.config.php';
    }
    
    public function getServiceConfig()
    {
        return include __DIR__ . '/config/services.config.php';
    }
    
    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'getSoundCategories' => function($sm) {
                    return new \Sound\View\Helper\GetSoundCategories($sm->getServiceLocator());    
                },
            ),
        );
    }
}

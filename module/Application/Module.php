<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        /*View helper*/
        $e->getApplication()->getServiceManager()->get('translator');
        $e->getApplication()->getServiceManager()->get('viewhelpermanager')->setFactory('navPrivilege', function($sm) use ($e) {
        $viewHelper = new View\Helper\NavPrivilege($e->getRouteMatch());
        $viewHelper->setServiceLocator($sm->getServiceLocator());
            return $viewHelper;
        });
        /*View helper*/
    
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    public function getViewHelperConfig()
    {
        return array(
            'invokables' => array(
                'wordLimit' => 'Application\View\Helper\WordLimit',
            ),
            'factories' => array(
                'getFullUrl' => function($sm) {
                    return new \Application\View\Helper\GetFullUrl($sm->getServiceLocator());    
                },
                'getS3Url' => function($sm) {
                    return new \Application\View\Helper\GetS3Url($sm->getServiceLocator());    
                },
            ),
        );
    }
    
    public function getControllerPluginConfig()
    {
        return array(
            'factories' => array(
                'permissionActions' => function($sm) {
                    return new \Application\Controller\Plugin\PermissionActions($sm->getServiceLocator());   
                },
                'getS3Url' => function($sm) {
                    return new \Application\Controller\Plugin\GetS3Url($sm->getServiceLocator());    
                },
                'getObjectsUrl' => function($sm) {
                    return new \Application\Controller\Plugin\GetObjectsUrl($sm->getServiceLocator());
                },
                'getFollowingUsers' => function($sm) {
                    return new \Application\Controller\Plugin\GetFollowingUsers($sm->getServiceLocator());
                },
            ),
        );
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(__dir__ . '/autoload_classmap.php', ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}

<?php

namespace Api;

use Zend\Mvc\MvcEvent;
use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\Db\TableGateway\TableGateway;

class Module
{
//    public function onBootstrap(MvcEvent $event)
//    {
//        $eventManager = $event->getApplication()->getEventManager();
//        
//        //echo '<pre>'; var_dump($event->getRouteMatch()->getParam('action', 'index')); echo '</pre>'; die;
//        //echo '<pre>'; var_dump($event->params()->fromRoute()); echo '</pre>'; die;
//        
//        
//        $eventManager->attach(MvcEvent::EVENT_DISPATCH, function (MvcEvent $event) {
//            //echo '<pre>'; var_dump($event->getRouteMatch()->getParams()); echo '</pre>'; die;
//            $params = $event->getRouteMatch()->getParams();
//            if($params['controller'] == 'User\Controller\UserRest' && $params['action'] == 'create' && !isset($params['sub_route'])) {
//                $check = true;
//            } else {
//                $check = false;
//            }
//            
//            if ($event->getTarget() instanceof AbstractRestfulController || $check == false) {
//                $authenticateService = $event->getApplication()
//                    ->getServiceManager()
//                    ->get('Api\Authenticate\AuthenticationService')
//                ;
//                
//                if ( ! $authenticateService->hasIdentity()) {
//                    // cannot access my apis
//                    die("cannot access my apis");
//                } else {
//                    //echo 'OK! Now you can access my apis<br/>';
//                    //var_dump($authenticateService->getIdentity());die;
//                }
//            }
//        });
//    }
    
//    public function onBootstrap(MvcEvent $e) {
//        $sharedManager = $e->getApplication()->getEventManager()->getSharedManager();
//        $sharedManager->attach('Zend\Mvc\Controller\AbstractRestfulController', MvcEvent::EVENT_DISPATCH, array($this, 'mvcOnDispatch'));
//    }
    
//    public function mvcOnDispatch(MvcEvent $event) {
//        $application = $event->getApplication();
//        $sm = $application->getServiceManager();
//
//        if ($sm->get('Api\Authenticate\AuthenticationService')->hasIdentity() == true) {
//        } else {
//            $params = $event->getRouteMatch()->getParams();
//            if($params['controller'] == 'User\Controller\UserRest' && $params['action'] == 'create' && !isset($params['sub_route'])) {
//                $check = true;
//            } else {
//                $check = false;
//            }
//            if ($check == false) {
//                die("cannot access my apis");
//                exit;
//            }
//        }
//
//    }
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getServiceConfig()
    {
      return array(
            'factories' => array(
                'Api\Model\TokenDao' =>  function($sm) {
                    $tableGateway = $sm->get('TokenTableGateway');
                    $table = new \Api\Model\Dao\TokenDao($tableGateway);
                    return $table;
                },
                'TokenTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new TableGateway('oauth_access_tokens', $dbAdapter, null);
                },
                'Api\Model\RefreshTokenDao' =>  function($sm) {
                    $tableGateway = $sm->get('RefreshTokenTableGateway');
                    $table = new \Api\Model\Dao\RefreshTokenDao($tableGateway);
                    return $table;
                },
                'RefreshTokenTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return new TableGateway('oauth_refresh_tokens', $dbAdapter, null);
                },         
            ),
        );
    }
}

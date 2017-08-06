<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

class NavPrivilege extends AbstractHelper
{
    protected $routeMatch;
    
    protected $serviceLocator;
    
    protected $permissionDao;

    public function __construct($routeMatch)
    {
        $this->routeMatch = $routeMatch;
    }
    
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
    
    public function setServiceLocator($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    
    public function getPermissionDao()
    {
        if(!$this->permissionDao) {
            $this->permissionDao = $this->getServiceLocator()->get('Permission\Model\PermissionDao');
        }
        return $this->permissionDao;
    }

    public function __invoke($resource)
    { 
        $auth = new \Zend\Authentication\AuthenticationService();
        if($auth->hasIdentity()) {
            $rid = $auth->getIdentity()->rid;    
        } else {
            $rid = 0;
        }
        
//        if($this->routeMatch) {
//            $controllerClass = $this->routeMatch->getParam('controller', 'index');
//            $moduleName     = substr($controllerClass, 0, strpos($controllerClass, '\\'));
//            $actionName = $this->routeMatch->getParam('action', 'index');
//            $controllerName = substr($controllerClass, strrpos($controllerClass, '\\') + 1, strlen($controllerClass) - 1);
//            $controllerName = str_ireplace('controller', '', $controllerName);
//            $resourceName   = strtolower($moduleName . '_' . $controllerName . '_' . $actionName);
//            //return $resourceName;    
//        } else {
//            $resourceName = null;    
//        }
        
        return $this->getPermissionDao()->fetchOne($rid,$resource);
        
    }
}
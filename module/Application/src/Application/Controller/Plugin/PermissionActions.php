<?php
namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class PermissionActions extends AbstractPlugin
{
    protected $serviceLocator;
    
    protected $permissionDao;
    
    public function __construct($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
    
    public function getPermissionDao()
    {
        if(!$this->permissionDao) {
            $this->permissionDao = $this->getServiceLocator()->get('Permission\Model\PermissionDao');
        }
        return $this->permissionDao;
    }
    
    /**
     * Get list permission of an user and decide to show some button like: add, edit, delete ... in listing view
     * 
     * @param mixed $actions
     * @param mixed $moduleControllerResource
     * @return array
     */
    public function __invoke($actions = array(),$moduleControllerResource)
    {  
        $auth = $this->getServiceLocator()->get('user_authenticate_service');
        $roleId = ($auth->getIdentity()) ? $auth->getIdentity()->rid : null;
        
        $permissions = array();
        foreach($actions as $action) {
            if($this->getPermissionDao()->fetchOne($roleId,$moduleControllerResource.'_'.$action)) {
                $permissions[$action] = 1;
            } else {
                $permissions[$action] = 0;    
            }
        }
        
        return $permissions;
    }
    
}
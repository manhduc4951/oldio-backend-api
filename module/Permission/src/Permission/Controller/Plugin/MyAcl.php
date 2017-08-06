<?php
namespace Permission\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin,
    Zend\Session\Container as SessionContainer, 
    Zend\Permissions\Acl\Acl, 
    Zend\Permissions\Acl\Role\GenericRole as Role, 
    Zend\Permissions\Acl\Resource\GenericResource as Resource;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Permission\Model\Business\PermissionBusiness;

class MyAcl extends AbstractPlugin implements ServiceManagerAwareInterface
{
    protected $sm;
    
    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->sm;
    }
    
    /**
     * Set service manager instance
     *
     * @param ServiceManager $locator
     * @return void
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->sm = $serviceManager;
    }
    
    public function getCurrentUserRole()
    {
        if ($this->getServiceManager()->get('user_authenticate_service')->hasIdentity()) {
            return $this->getServiceManager()->get('user_authenticate_service')->getIdentity()->role_name;
                
        }
        return 'anonymous';
    }
    
    public function doAuthorization($e)
    {
        $currentRole = $this->getCurrentUserRole();
        //echo $currentRole; die;
        if ($currentRole === 'administrator') return true;
        
        $controller = $e->getTarget();
        $controllerClass = get_class($controller);
        $moduleName     = substr($controllerClass, 0, strpos($controllerClass, '\\'));
        $controllerName = substr($controllerClass, strrpos($controllerClass, '\\') + 1, strlen($controllerClass) - 1);
        $controllerName = str_ireplace('controller', '', $controllerName);
        $actionName     = strtolower($e->getRouteMatch()->getParam('action', 'index'));
        $resourceName   = strtolower($moduleName . '_' . $controllerName);
        
        $aclConfig = $this->getServiceManager()->get('Permission\Model\Business\Permission')->getPermissionMatrix();
        $aclConfig['resources'] = empty($aclConfig['resources']) ? array() : $aclConfig['resources'];
        
        //setting ACL...
        $acl = new Acl();
      
        // Add role
        if(is_array($aclConfig['roles']) && !empty($aclConfig['roles'])) {
            foreach ($aclConfig['roles'] as $role) {
                $acl->addRole(new Role($role));
            }
        }

        // Add resource
        if(is_array($aclConfig['resources']) && !empty($aclConfig['resources'])) {
            foreach ($aclConfig['resources'] as $module => $controllers) {
                foreach ($controllers as $controller => $roles) {
                    $resource = strtolower("{$module}_{$controller}");
                    $acl->addResource(new Resource($resource));
                    
                    foreach ($roles as $roleName => $privileges) {
                        foreach ($privileges as $action => $privilege) {
                            if (!$privilege) {
                                $acl->deny($roleName, $resource, $action);
                            } else {
                                $acl->allow($roleName, $resource, $action);
                            }
                        }
                    }
                }
            }
        }
        
        
        //echo '<pre>'; print_r($resourceName); echo '</pre>'; die;
        
        $router = $e->getRouter();
//        if (!$acl->hasResource($resourceName)) {
//            die('if');
//            $url = $router->assemble(array(), array('name' => 'auth'));
//        } else {
//            die('else');
//            if ($actionName !== 'login' && $actionName !== 'generate-acl') {die('tytyt');
//                if (!$acl->isAllowed($currentRole, $resourceName, $actionName)) {die('uyuy');
//                    $router = $e->getRouter();
//                    $url = $router->assemble(array(), array('name' => 'auth'));
//                }
//            }
//        }
        if(!$acl->hasResource($resourceName)) {
            if($actionName !== 'login') {
                $url = $router->assemble(array(), array('name' => 'auth'));     
            }               
        } else {
            if($actionName !== 'login' && $actionName !== 'generate-acl') {
                if (!$acl->isAllowed($currentRole, $resourceName, $actionName)) {//die('uyuy');
                        $router = $e->getRouter();
                        $url = $router->assemble(array(), array('name' => 'auth'));
                }    
            }    
        }
        
        
        
        
        
        //echo '<pre>'; print_r($url); echo '</pre>'; die;       
        if (!empty($url)) {
            $response = $e->getResponse();
            $response->setStatusCode(302);
            $response->getHeaders()->addHeaderLine('Location', $url);
            $e->stopPropagation();
        }
    }
}

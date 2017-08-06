<?php
namespace Permission\Model\Business;

use Permission\Form\PermissionForm;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Cache\Storage\StorageInterface;

class PermissionBusiness implements ServiceLocatorAwareInterface
{
    protected $serviceLocator;
    
    protected $cache;
    
    const ACL_CACHE_ID = 'acl_resource';
    
    public function setServiceLocator(ServiceLocatorInterface $sl)
    {
        $this->serviceLocator = $sl;
    }
    
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function setCache(StorageInterface $cache)
    {
        $this->cache = $cache;
    }
    
    public function _scanControllerAction()
    {
        $directory = './module';
        $actions   = array();
        
        foreach (scandir($directory) as $module) {
            if ($module === '.' || $module === '..' || !is_dir($directory . '/' . $module)) {
                continue;
            }
             
            $controllerFolder = $directory . '/' . $module . '/src/' . $module . '/Controller';
            //echo $controllerFolder.'</br>';
             
            if (!is_dir($controllerFolder)) {
                continue;
            }
            //echo $controllerFolder.'</br>';
             
            foreach (scandir($controllerFolder) as $subFolder) {
                if ($subFolder === '.' || $subFolder === '..' || is_dir($controllerFolder . '/' . $subFolder)) {
                    continue;
                }
                 
                $controllerName = explode('.', $subFolder);
                if(strpos($controllerName[0],'Rest') !== false) {
                    //die;
                    continue;
                }
                //echo $controllerName[0].'</br>';
                foreach (get_class_methods("$module\Controller\\$controllerName[0]") as $action) {
                    $action = strtolower($action);
                    if (substr($action, -6) === "action") {
                        $actionName = substr($action, 0, -6);
                         
                        if (!in_array($actionName, array('notfound', 'getmethodfrom'))) {
                            $controller = str_ireplace('controller', '', $controllerName[0]);
                            $actions[strtolower($module)][strtolower($controller)][] = $actionName;
                        }
                    }
                }
                
            }
        }
         
        return $actions;        
    }
    
    public function scanControllerAction()
    {
        $data     = $this->_scanControllerAction();
        //echo '<pre>'; var_dump($data); echo '</pre>'; die;
        $resouces = $this->getPermissionMatrix();
        $matrix   = array();
        
        foreach ($data as $module => $controllers) {
            foreach ($controllers as $controller => $actions) {
                foreach ($this->getRoles() as $roleId => $roleName) {
                    foreach ($actions as $action) {
                        if (!isset($resouces[strtolower($module)][strtolower($controller)][$roleName][$action])) {
                            $matrix[] = array('rid' => $roleId, 'module_controller_action' => "{$module}_{$controller}_{$action}", 'privilege' => ($roleId == 1) ? 1 : 0);
                        }
                    }
                }
            }
        }
        
        if ($matrix) {
            $this->getServiceLocator()->get('Permission\Model\PermissionDao')->save($matrix);
            $this->cache->removeItem(self::ACL_CACHE_ID);
            $this->cache->setItem(self::ACL_CACHE_ID, $this->getPermissionMatrix());
        }
    }
    
    public function getPermissionMatrix()
    {        
        //$this->cache->removeItem(self::ACL_CACHE_ID);
        if (!$matrix = $this->cache->getItem(self::ACL_CACHE_ID)) {
            $matrix = array('roles' => $this->getRoles()); 
            $permissionDao = $this->getServiceLocator()->get('Permission\Model\PermissionDao');
            $data = $permissionDao->fetchAll(); 
            
            if (!$data) return $matrix;
            $roles = self::getRoles();
            
            foreach ($data as $d) {
                $resource = explode('_', $d['module_controller_action']);
                $matrix['resources'][strtolower($resource[0])][strtolower($resource[1])][$roles[$d['rid']]][$resource[2]] = $d['privilege'];
            }
            
            try {                
                $this->cache->setItem(self::ACL_CACHE_ID, $matrix);
            } catch(Exception $e) {
              echo $e->getMessage();
              die('hehe');
            }
        }
        
        return $matrix;
    }
    
    public function generatePrivilegeCheckboxes()
    {
        $form = new PermissionForm;
        $matrix = $this->getPermissionMatrix();
        
        if (empty($matrix['resources'])) {
            return $form;
        }
        
        foreach ($matrix['resources'] as $module => $controllers) {
            foreach ($controllers as $controller => $roles) { 
                foreach ($roles as $role => $actions) {
                    foreach ($actions as $action => $val) {
                        $form->add(array(
                            'name' => "permission[$module][$controller][$role][$action]",
                            'type' => 'checkbox',
                            'options' => array(
                                'use_hidden_element' => false,
                                'checked_value' => 1,
                                'unchecked_value' => 0,
                            ),
                            'attributes' => array(
                                'checked' => (bool) $val
                            )
                        ));
                    }
                }
            }
        }
        
        return $form;
    }
    
    public function generateAclCache($post)
    {
        $this->cache->removeItem(self::ACL_CACHE_ID);
        $permissionDao = $this->getServiceLocator()->get('Permission\Model\PermissionDao');
        
        $resources = $this->getPermissionMatrix();
        $dto = array();
        $roleIds = array_flip($resources['roles']);
        
        foreach ($resources['resources'] as $module => $controllers) {
            foreach ($controllers as $controller => $roles) { 
                foreach ($roles as $role => $privileges) {
                    foreach ($privileges as $action => $privilege) {
                        if (!empty($post[$module][$controller][$role][$action])) {
                            $privilege = $post[$module][$controller][$role][$action];
                        } elseif (!empty($post)) {
                            $privilege = 0;
                        }
                        
                        $resources['resources'][$module][$controller][$role][$action] = $privilege;
                        $dto[] = array('rid' => $roleIds[$role], 'module_controller_action' => "{$module}_{$controller}_{$action}", 'privilege' => $privilege);
                    }
                }
            }
        }
        
        $permissionDao->deleteAll();
        $permissionDao->save($dto);
                 
        $this->cache->setItem(self::ACL_CACHE_ID, $resources);
    }
    
    public function getRoles()
    {
        
        return $roles = array(
            1 => 'administrator',
            2 => 'authenticated user',
            3 => 'anonymous',
            //4 => 'testuser',
            //5 => 'foo',
        );
    }
}

<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Permission for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Permission\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class IndexController extends AbstractActionController
{
    protected $permissionBusiness;
    
    protected $permissionDao;
    
	public function onDispatch(\Zend\Mvc\MvcEvent $e)
	{
		parent::onDispatch($e);
		
		$this->layout()->backend = 'backend';
	}
	
    public function getPermissionBusiness()
    {
        if (!$this->permissionBusiness) {
            $this->permissionBusiness = $this->getServiceLocator()->get('Permission\Model\Business\Permission');
        }
        
        return $this->permissionBusiness;
    }
    
    public function getPermissionDao()
    {
        if(!$this->permissionDao) {
            $this->permissionDao = $this->getServiceLocator()->get('Permission\Model\PermissionDao');
        }
        return $this->permissionDao;
    }
    
    public function indexAction()
    {
        if ($this->getRequest()->isPost()) {
            if ($this->getRequest()->getPost('update')) {
               $this->getPermissionBusiness()->generateAclCache($this->getRequest()->getPost('permission'));
            }
            
            if ($this->getRequest()->getPost('scan')) {
                $this->getPermissionDao()->deleteAll();
                $this->getPermissionBusiness()->scanControllerAction();
            }
            
            return $this->redirect()->toRoute('permission');
        }
        
        $data = $this->getPermissionBusiness()->getPermissionMatrix();

        return array(
            'permission_form' => $this->getPermissionBusiness()->generatePrivilegeCheckboxes(), 
            'data' => empty($data['resources']) ? array() : $data['resources'], 
            'roles' => $this->getPermissionBusiness()->getRoles(),
        );
    }

}

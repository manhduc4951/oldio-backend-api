<?php
namespace AdminUser\Business;

use Zend\Di\ServiceLocator;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class AdminUserBusiness implements ServiceManagerAwareInterface
{	
 	protected $serviceManager;
    
    protected $adminUserDao;
    
    protected $userRoleDao;
 	
 	public function setServiceManager(ServiceManager $serviceManager)
 	{
 	    $this->serviceManager = $serviceManager;
 	}
 	
 	public function getServiceManager()
 	{
 	    return $this->serviceManager;
 	}
    
    public function getAdminUserDao()
    {
        if(!$this->adminUserDao) {
            $this->adminUserDao = $this->getServiceManager()->get('AdminUser\Model\AdminUserDao');    
        }
        return $this->adminUserDao;
    }
    
    public function getUserRoleDao()
    {
        if(!$this->userRoleDao) {
            $this->userRoleDao = $this->getServiceManager()->get('Permission\Model\Dao\UserRoleDao');    
        }
        return $this->userRoleDao;
    }
    
    /**
     * Create/Add Admin User
     * 
     * @param mixed $data
     * @return void
     */
    public function createAdminUser($data)
    {
         try {
             $this->getServiceManager()->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection()->beginTransaction();
             
             $adminUser = new \AdminUser\Model\Dto\AdminUserDto;
             $adminUser->exchangeArray($data);
             $adminUser->password = md5($adminUser->password);
             $adminUser->created_at = date('Y-m-d H:i:s');
             $adminUser->updated_at = date('Y-m-d H:i:s');
             $uid = $this->getAdminUserDao()->save($adminUser);
             
             $userRole = new \Permission\Model\Dto\UserRole;
             $userRole->uid = $uid;
             $userRole->rid = $data['role'];
             $this->getUserRoleDao()->add($userRole);
             $this->getServiceManager()->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection()->commit();
         } catch (\Exception $e) {
             die($e->getMessage());
             $this->getServiceManager()->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection()->rollback();
         }
    }
    
    public function updateAdminUser($data)
    {  
        try {
             $this->getServiceManager()->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection()->beginTransaction();
             
             $adminUser = new \AdminUser\Model\Dto\AdminUserDto;
             $adminUser->exchangeArray($data);
             if($data['password']) {
                $adminUser->password = md5($data['password']);   
             }
             $adminUser->updated_at = date('Y-m-d H:i:s');
             $this->getAdminUserDao()->save($adminUser);
             
             $userRole = new \Permission\Model\Dto\UserRole;
             $userRole->uid = $data['uid'];
             $userRole->rid = $data['role'];
             $this->getUserRoleDao()->update($userRole);
             $this->getServiceManager()->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection()->commit();
         } catch (\Exception $e) {
             die($e->getMessage());
             $this->getServiceManager()->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection()->rollback();
         }
    }
    
    public function deleteAdminUser($uid)
    {
        try {
             $this->getServiceManager()->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection()->beginTransaction();
             $uid = (int) $uid;
             $this->getUserRoleDao()->delete($uid);
             $this->getAdminUserDao()->delete($uid);
             $this->getServiceManager()->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection()->commit();
         } catch (\Exception $e) {
             die($e->getMessage());
             $this->getServiceManager()->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection()->rollback();
         }
    }
    
}
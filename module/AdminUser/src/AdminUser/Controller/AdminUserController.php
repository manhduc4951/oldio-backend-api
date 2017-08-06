<?php
namespace AdminUser\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class AdminUserController extends AbstractActionController
{
    protected $adminUserDao;
    
    protected $roleNameDao;
    
    protected $permissionDao;
    
    protected $adminUserBusiness;
    
    public function getAdminUserDao()
    {
        if(!$this->adminUserDao) {
            $sm = $this->getServiceLocator();
            $this->adminUserDao = $sm->get('AdminUser\Model\AdminUserDao');
        }
        return $this->adminUserDao;
    }
    
    public function getRoleNameDao()
    {
        if(!$this->roleNameDao) {
            $sm = $this->getServiceLocator();
            $this->roleNameDao = $sm->get('Permission\Model\RoleNameDao');
        }
        return $this->roleNameDao;
    }
    
    public function getPermissionDao()
    {
        if(!$this->permissionDao) {
            $sm = $this->getServiceLocator();
            $this->permissionDao = $sm->get('Permission\Model\PermissionDao');
        }
        return $this->permissionDao;
    }
    
    public function getAdminUserBusiness()
    {
        if(!$this->adminUserBusiness) {
            $sm = $this->getServiceLocator();
            $this->adminUserBusiness = $sm->get('AdminUser\Business\AdminUserBussiness');
        }
        return $this->adminUserBusiness;
    }
    
    public function indexAction()
    { 
        $config = $this->getServiceLocator()->get('Config');        
        
        /*Fetch data && paginate*/
        $request = $this->getRequest();
        $query = null;
        if($request->isGet()) {            
            $query = $this->params()->fromQuery();
            if(!empty($query['created_at'])) {
                $created = explode(' - ',$query['created_at']);
                $query['created_at_from'] = (!empty($created[0])) ? $created[0].' 00:00:00' : null;
                $query['created_at_to'] = (!empty($created[1])) ? $created[1].' 23:59:59' : null;
            }                                
        }
        $data = $this->getAdminUserDao()->fetchAll(true,$query); 
        $paginator = $data['paginator'];        
        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));        
        $paginator->setItemCountPerPage($config['config_ica467']['item_per_page']);
        $page = (int)$this->params()->fromQuery('page', 1);        
        
        /*Form search*/
        $formSearch = $this->getAdminUserFormSearch();
        if($data['query']) {
            $formSearch->setData($data['query']);            
        }
        
        /*Flash message*/
        $messages = '';
        $flashMessenger = $this->flashMessenger();
        if ($flashMessenger->hasMessages()) {
               $messages = $flashMessenger->getMessages();
        }
        
        return array(
            'users' => $paginator,
            'messages' => $messages,
            'page' => $page,
            'formSearch' => $formSearch,
            'permissions' => $this->permissionActions(array('add','edit','delete'),'adminuser_adminuser'),
        );
    }
    
    public function addAction()
    {
        $form = $this->getAdminUserForm();
        
        $request = $this->getRequest();
        if($request->isPost()) {
            $formFilter = new \AdminUser\Form\AdminUserFormFilter;
            $formFilter->setDbAdapter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
            $form->setInputFilter($formFilter->getInputFilter());
            $form->setData($request->getPost());
            if($form->isValid()) {
                $this->getAdminUserBusiness()->createAdminUser($form->getData());
                $this->flashMessenger()->addMessage('Create admin user successfully');
			    return $this->redirect()->toRoute('admin-user');
            } 
        }
        
        return array(
            'form' => $form,
        );
    }
    
    public function editAction()
    {
        $uid = $this->params()->fromRoute('uid',0);
        if(!$uid) {
            return $this->redirect()->toRoute('admin-user', array(
                'action' => 'add'
            ));
        }
        $request = $this->getRequest();
        
        $adminUser = $this->getAdminUserDao()->getUserInfo($uid);
        if(!$adminUser) {
            return $this->redirect()->toRoute('admin-user');
        }
        $adminUser->role = $adminUser->rid;
        $adminUser->password = null;
        
        $form = $this->getAdminUserForm();
        $form->setValidationGroup('uid','password','confirm_password','role','status');
        $form->get('submit')->setValue('Save');
        $form->get('username')->setAttribute('disabled','disabled');
        $form->bind($adminUser);
        
        if($request->isPost()) {
            $formFilter = new \AdminUser\Form\AdminUserFormFilter;
            $formFilter->setDbAdapter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
            $data = $request->getPost();
            if(empty($data['password']) && empty($data['confirm_password'])) {
                $formFilter->getInputFilter()->get('password')->setAllowEmpty(true);
                $formFilter->getInputFilter()->get('confirm_password')->setAllowEmpty(true);
                unset($data['password']);
                unset($data['confirm_password']);
            }
            $form->setInputFilter($formFilter->getInputFilter());
            $form->setData($data);
            if($form->isValid()) {
                $this->getAdminUserBusiness()->updateAdminUser($adminUser);
                $this->flashMessenger()->addMessage('Update admin user successfully');
			    return $this->redirect()->toRoute('admin-user');
            }
        }
        
        return array(
            'form' => $form,
            'uid' => $uid,
        );
    }
    
    public function changePasswordAction()
    {
        //$user = $this->getServiceLocator()->get('user_authenticate_service')->getIdentity();
        $adminUser = $this->getAdminUserDao()->fetchOne($this->getServiceLocator()->get('user_authenticate_service')->getIdentity()->uid);
        $adminUser->password = null;
        //echo '<pre>'; var_dump($user); echo '</pre>'; die;
        $form = new \AdminUser\Form\ChangePasswordForm;
        $form->bind($adminUser);
        $request = $this->getRequest();
        
        if($request->isPost()) {
            $formFilter = new \AdminUser\Form\ChangePasswordFormFilter;
            $form->setInputFilter($formFilter->getInputFilter());
            //echo '<pre>'; var_dump($adminUser); echo '</pre>'; die;
            $data = $request->getPost();
            $form->setData($data);
            if($form->isValid()) {
                //echo '<pre>'; var_dump($adminUser); echo '</pre>'; die;
                $adminUser->password = md5($adminUser->password);
                $adminUser->updated_at = date('Y-m-d H:i:s');
                $this->getAdminUserDao()->save($adminUser);
                return $this->redirect()->toRoute('home');
            }
            
        }
        
        return array(
            'form' => $form,
        );
    }
    
    public function deleteAction()
    {
        $uid = $this->params()->fromRoute('uid',0);
        if(!$uid) {
            return $this->redirect()->toRoute('admin-user');    
        }
        $this->getAdminUserBusiness()->deleteAdminUser($uid);
        return $this->redirect()->toRoute('admin-user');
        die;
    }
    
    public function getAdminUserForm()
    {
        $user = $this->getServiceLocator()->get('user_authenticate_service')->getIdentity();
        
        $form = new \AdminUser\Form\AdminUserForm;
        $roles = $this->getRoleNameDao()->fetchAll($user->rid);
        $role_array = array();
        foreach($roles as $role) {
            $role_array[$role->rid] = $role->name;
        }
        $form->get('role')->setOptions(array('value_options' => $role_array));
        
        return $form;
    }
    
    public function getAdminUserFormSearch()
    {
        $formSearch = new \AdminUser\Form\AdminUserFormSearch;
        $roles = $this->getRoleNameDao()->fetchAll();
        $role_array = array();
        foreach($roles as $role) {
            $role_array[$role->rid] = ucwords($role->name);
        }
        $formSearch->get('rid')->setOptions(array('value_options' => $role_array));
        
        return $formSearch;    
    }
    
    
}
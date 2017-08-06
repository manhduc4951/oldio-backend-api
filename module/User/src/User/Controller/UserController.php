<?php
namespace User\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class UserController extends AbstractActionController
{
    protected $userDao;
    
    protected $countryDao;
    
    protected $permissionDao;
    
    protected $userBusiness;
    
    public function getUserDao()
    {
        if(!$this->userDao) {
            $sm = $this->getServiceLocator();
            $this->userDao = $sm->get('User\Model\UserDao');
        }
        return $this->userDao;
    }
    
    public function getCountryDao()
    {
        if (!$this->countryDao) {
            $sm = $this->getServiceLocator();
            $this->countryDao = $sm->get('User\Model\CountryDao');
        }
        return $this->countryDao;
    }
    
    public function getPermissionDao()
    {
        if(!$this->permissionDao) {
            $sm = $this->getServiceLocator();
            $this->permissionDao = $sm->get('Permission\Model\PermissionDao');
        }
        return $this->permissionDao;
    }
    
    public function getUserBusiness()
    {
        if(!$this->userBusiness) {
            $sm = $this->getServiceLocator();
            $this->userBusiness = $sm->get('User\Business\UserBussiness');
        }
        return $this->userBusiness;
    }
    
    /**
     * Listing all app user and show in backend
     * 
     * @return
     */
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
            if(!empty($query['birthday'])) {
                $birthday = explode(' - ',$query['birthday']);
                $query['birthday_from'] = (!empty($birthday[0])) ? $birthday[0] : null;
                $query['birthday_to'] = (!empty($birthday[1])) ? $birthday[1] : null;
            }                              
        }
        $data = $this->getUserDao()->fetchAllUser(true,$query); 
        $paginator = $data['paginator'];        
        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));        
        $paginator->setItemCountPerPage($config['config_ica467']['item_per_page']);
        $page = (int)$this->params()->fromQuery('page', 1);        
        
        /*Form search*/
        $formSearch = new \User\Form\UserFormSearch;
        $formSearch->get('country_id')->setOptions(array('value_options' => $this->getCountries()));
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
            'permissions' => $this->permissionActions(array('add','edit','delete','view'),'user_user'),
        );
    }
    
    /**
     * View details information of an app user from backend
     * 
     * @return
     */
    public function viewAction()
    {
        $userId = $this->params()->fromRoute('id',0);
        $user = $this->getUserDao()->fetchOneDetail((int)$userId);
        return array(
            'user' => $user,
        );
    }
    
    /**
     * Create a new app user from backend
     * 
     * @return
     */
    public function addAction()
    {
        $form = new \User\Form\UserForm;
        $form->get('country_id')->setOptions(array('value_options' => $this->getCountries()));
        
        $request = $this->getRequest();
        if($request->isPost()) {
            $formFilter = new \User\Form\UserFormFilter;
            $formFilter->setDbAdapter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
            $formFilter->setServiceLocator($this->getServiceLocator());
            $form->setInputFilter($formFilter->getInputFilter());
            $data = array_merge($request->getPost()->toArray(),$_FILES);
            $form->setData($data);
            if($form->isValid()) {
                $user = new \User\Model\Dto\UserDto;
                $user->exchangeArray($form->getData());
                $this->getUserBusiness()->createUser($user);
                $this->flashMessenger()->addMessage('Create user successfully');
			    return $this->redirect()->toRoute('user');
            }
        }
        
        return array(
            'form' => $form,
        );
    }
    
    /**
     * Edit information of an app user from backend
     * 
     * @return
     */
    public function editAction()
    {
        $id = $this->params()->fromRoute('id',0);
        if(!$id) {
            return $this->redirect()->toRoute('user', array(
                'action' => 'add'
            ));
        }
        $request = $this->getRequest();
        $user = $this->getUserDao()->fetchOne($id);
        if(!$user) {
            return $this->redirect()->toRoute('user');
        }
        $oldAvatar = $user->avatar;
        $oldCoverImage = $user->cover_image;
        $user->password = null;
        $form = new \User\Form\UserForm;
        $form->get('country_id')->setOptions(array('value_options' => $this->getCountries()));
        $form->setValidationGroup('id','password','full_name','display_name','avatar','cover_image','phone','birthday','gender','country_id','description');
        $form->get('username')->setAttribute('disabled','disabled');
        $form->bind($user);
        
        if($request->isPost()) {
            $formFilter = new \User\Form\UserFormFilter;
            $formFilter->setDbAdapter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
            $formFilter->setServiceLocator($this->getServiceLocator());
            $data = array_merge($request->getPost()->toArray(),$_FILES);
            if(empty($data['password'])) {
                $formFilter->getInputFilter()->get('password')->setAllowEmpty(true);
            }
            $displayNameExclude = $formFilter->getInputFilter()->get('display_name')->getValidatorChain()->getValidators();
            $displayNameExclude[1]['instance']->setExclude(array('field'=>'id','value'=>$id));
            $form->setInputFilter($formFilter->getInputFilter());
            $form->setData($data);
            if($form->isValid()) {
                $user->old_avatar = $oldAvatar;
                $user->old_cover_image = $oldCoverImage;
                $this->getUserBusiness()->updateUser($user);
                $this->flashMessenger()->addMessage('Update user successfully');
			    return $this->redirect()->toRoute('user');
            }
        }
        $config = $this->getServiceLocator()->get('config');
        $coverImage = ($user->cover_image) ? $this->getS3Url($config['config_ica467']['user_cover_path_upload'].'/'.$user->cover_image) : null;
        $avatar = ($user->avatar) ? $this->getS3Url($config['config_ica467']['user_avatar_path_upload'].'/'.$user->avatar) : null;
        return array(
            'form' => $form,
            'id' => $id,
            'files' => array(
                'avatar' => $avatar,
                'cover_image' => $coverImage,
            ),
        );
    }
    
    /**
     * Delete app user from backend
     * 
     * @return void
     */
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id',0);
        if(!$id) {
            return $this->redirect()->toRoute('user',array('action' => 'index'));
        }
        $user = $this->getUserDao()->fetchOne($id);        
        $this->getUserBusiness()->deleteUser($user);
        return $this->redirect()->toRoute('user',array('action'=>'index'));
    }
    
    /**
     * Get list all countries and bind to form
     * 
     * @return void
     */
    public function getCountries()
    {  
        $countries = $this->getCountryDao()->fetchAll();
        $country_array = array();
        foreach($countries as $country) {
            $country_array[$country->code] = $country->name;
        }
        
        return $country_array; 
    }
}
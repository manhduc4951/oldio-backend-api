<?php
namespace User\Controller;

use Api\Controller\AbstractMyRestfulController;
use Zend\View\Model\JsonModel;

class UserRestController extends AbstractMyRestfulController
{  
    protected $userDao;
    
    protected $soundDao;
    
    protected $countryDao;
    
    protected $storagePlanDao;
    
    protected $userBusiness;
    
    public function getUserDao()
    {
        if (!$this->userDao) {
            $sm = $this->getServiceLocator();
            $this->userDao = $sm->get('User\Model\UserDao');
        }
        return $this->userDao;
    }
    
    public function getSoundDao()
    {
        if(!$this->soundDao) {
            $sm = $this->getServiceLocator();
            $this->soundDao = $sm->get('Sound\Model\SoundDao');
        }
        return $this->soundDao;
    }
    
    public function getCountryDao()
    {
        if (!$this->countryDao) {
            $sm = $this->getServiceLocator();
            $this->countryDao = $sm->get('User\Model\CountryDao');
        }
        return $this->countryDao;
    }
    
    public function getStoragePlanDao()
    {
        if(!$this->storagePlanDao) {
            $sm = $this->getServiceLocator();
            $this->storagePlanDao = $sm->get('Settings\Model\StoragePlanDao');
        }
        return $this->storagePlanDao;
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
     * Get details of an user
     * 
     * @param mixed $id
     * @return
     */
    public function get($id)
    {  
        $userId = (int) $id;
        if(!$this->getUserDao()->fetchOne($userId)) {
            return $this->error('The user you request does not exist');
        }
        $myUserId = $this->identity()->id;
        $countPrivate = true;
        if($myUserId == $userId || in_array($myUserId,$this->getFollowingUsers($userId))) {
            $countPrivate = false;
        }
        
        $user = $this->getUserDao()->fetchOneDetail($userId,array('count_private' => $countPrivate));
        $filePaths = array(
            'avatar' => 'user_avatar_path_upload',
            'cover_image' => 'user_cover_path_upload',
        );
        $user = $this->getObjectsUrl($user,$filePaths,'one');
        return $this->success($user);
    }
    
    

    /**
     * Register user
     * 
     * @param mixed $data
     * @return
     */
    public function create($data)
    {  
        // upload avatar
        $params = $this->params()->fromRoute();
        if(isset($params['sub_route']) and $params['sub_route'] == 'avatar') {            
           return $this->uploadAvatar();            
        }
        // upload cover image
        if(isset($params['sub_route']) and $params['sub_route'] == 'cover_image') {            
           return $this->uploadCoverImage();            
        }
        
        $form = new \User\Form\RegisterForm;
        $formFilter = new \User\Form\RegisterFormFilter;
        $formFilter->setDbAdapter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        $form->setInputFilter($formFilter->getInputFilter());        
        $form->setData($data);
        if ($form->isValid()) {
            $dataForm = $form->getData();
            $userDto = new \User\Model\Dto\UserDto;
            $userDto->exchangeArray($dataForm);
            $user = $this->getUserBusiness()->createUserRest($userDto);
            
            return $this->success($user);
        } else {
            $error = $form->getMessages();
            if(isset($error['username']['recordFound'])) {
                return $this->failure('Email existed, please choose another');    
            } else {
                return $this->formInvalid();    
            }
        }

    }

    /**
     * Update profile
     * 
     * @param mixed $id
     * @param mixed $data
     * @return
     */
    public function update($id, $data)
    {
        /*Check user validate*/
        $user_id = (int) $id;
        $user = $this->getUserDao()->fetchOne($user_id);        
        if(!$user) {
            return $this->error('The user you request does not exist');
        }
        if($user_id != $this->identity()->id) {
            return $this->error('You can not update profile which does not belong to you',400);    
        }
        
        /*Redirect to another action depend on request*/
        $params = $this->params()->fromRoute();
        if(isset($params['sub_route']) and $params['sub_route'] == 'storage-plan') {            
           return $this->updateStoragePlan($data);           
        }
        if(isset($params['sub_route']) and $params['sub_route'] == 'update-facebook-info') {            
           return $this->updateFacebookInfo($data,$user);           
        }
        if(isset($params['sub_route']) and $params['sub_route'] == 'change-password') {
           return $this->changePassword($data,$user);           
        }
        
        $form = new \User\Form\UserForm;
        $form->bind($user);
        $formFilter = new \User\Form\UserFormFilter;
        $formFilter->setDbAdapter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        $formFilter->setServiceLocator($this->getServiceLocator());
        $formFilter->getInputFilter()->get('username')->setAllowEmpty(true); 
        $formFilter->getInputFilter()->get('password')->setAllowEmpty(true);
        $displayNameExclude = $formFilter->getInputFilter()->get('display_name')->getValidatorChain()->getValidators();
        $displayNameExclude[1]['instance']->setExclude(array('field'=>'id','value'=>$user_id));
        $form->setInputFilter($formFilter->getInputFilter());
        $form->setData($data);
        if($form->isValid()) {
            $user->updated_at = date('Y-m-d H:i:s');
            $this->getUserDao()->save($user);
            return $this->success($user->getArrayCopy());    
        } else {
            return $this->formInvalid();
        }
    }
    
    /**
     * Change password of an user
     * 
     * @param mixed $data
     * @param mixed $user
     * @return void
     */
    public function changePassword($data,$user)
    {
        if($this->identity()->password != md5($data['current_password'])) {
            return $this->failure('Please check your current password');
        }
        $form = new \User\Form\ChangePasswordForm;
        $formFilter = new \User\Form\ChangePasswordFormFilter;
        $form->setInputFilter($formFilter->getInputFilter());
        $form->setData($data);
        if($form->isValid()) {
            $user->password = md5($data['new_password']);
            $user->updated_at = date('Y-m-d H:i:s');
            $this->getUserDao()->save($user);
            return $this->success();
        }
        return $this->formInvalid(); 
    }
    
    /**
     * Update facebook information of an user
     * 
     * @param mixed $data
     * @param mixed $user
     * @return void
     */
    public function updateFacebookInfo($data,$user)
    {
        $form = new \User\Form\UserForm;
        $form->bind($user);
        $formFilter = new \User\Form\UserFormFilter;
        $formFilter->setDbAdapter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        $formFilter->setServiceLocator($this->getServiceLocator());
        $form->setInputFilter($formFilter->getInputFilter());
        $form->setValidationGroup('facebook_id');
        $form->setData($data);
        if($form->isValid()) {
            $user->updated_at = date('Y-m-d H:i:s');
            $this->getUserDao()->save($user);
            return $this->success();
        } else {
            return $this->formInvalid();
        }
    }
    
    /**
     * Update storage plan of a user
     * 
     * @param mixed $data
     * @return
     */
    public function updateStoragePlan($data)
    {
        $userId = $this->identity()->id;
        $storagePlanId = (int) $data['storage_plan_id'];
        if(!$this->getStoragePlanDao()->fetchOne($storagePlanId)) {
            return $this->error('This storage plan does not exist');
        }
        $user = $this->getUserDao()->fetchOne($userId);
        $user->storage_plan_id = $storagePlanId;
        $user->storage_plan_updated_at = date('Y-m-d H:i:s',time() + 365*86400);
        $user->updated_at = date('Y-m-d H:i:s');
        $this->getUserDao()->save($user);
        
        return $this->success();
    }
    
    /**
     * upload avatar
     * 
     * @return
     */
    public function uploadAvatar()
    {  
        $user_id = $this->params()->fromRoute('id',0);
        if($user_id != $this->identity()->id) {
            return $this->error('This user does not belong to you so you can not edit avatar',400);
        }
        $user = $this->getUserDao()->fetchOne($user_id);
        if(!$user) {
            return $this->error('The user you request does not exist');
        }
         
        $config = $this->getServiceLocator()->get('config');            
        $oldAvatar = $user->avatar;
        
        $form = new \User\Form\UploadAvatarForm;
        $form->bind($user);
        $formFilter = new \User\Form\UploadAvatarFormFilter;        
        $formFilter->setServiceLocator($this->getServiceLocator());
        $form->setInputFilter($formFilter->getInputFilter());
        $data = array_merge($user->getArrayCopy(),$_FILES);
        $form->setData($data);
        if($form->isValid()) {
            $user->old_avatar = $oldAvatar;
            //$userAvatar = $this->url()->fromRoute('user_avatar', array('file' => $this->getUserBusiness()->uploadAvatarRest($user)),array('force_canonical' => true));
            //s3
            $userAvatar = $this->getS3Url($config['config_ica467']['user_avatar_path_upload'].'/'.$this->getUserBusiness()->uploadAvatarRest($user));
            return $this->success(array('avatar' => $userAvatar));
        }                
        return $this->formInvalid();
        
    }
    
    /**
     * upload cover image
     * 
     * @return
     */
    public function uploadCoverImage()
    {  
        $user_id = $this->params()->fromRoute('id',0);
        if($user_id != $this->identity()->id) {
            return $this->error('This user does not belong to you so you can not edit cover image',400);
        }
        $user = $this->getUserDao()->fetchOne($user_id);
        if(!$user) {
            return $this->error('The user you request does not exist');
        }
         
        $config = $this->getServiceLocator()->get('config');            
        $oldCoverImage = $user->cover_image;
        
        $form = new \User\Form\UploadCoverImageForm;
        $form->bind($user);
        $formFilter = new \User\Form\UploadCoverImageFormFilter;      
        $formFilter->setServiceLocator($this->getServiceLocator());
        $form->setInputFilter($formFilter->getInputFilter());
        
        $data = array_merge($user->getArrayCopy(),$_FILES);
        $form->setData($data);
        if($form->isValid()) {
            $user->old_cover_image = $oldCoverImage;
            //$coverImage = $this->url()->fromRoute('user_cover_image', array('file' => $this->getUserBusiness()->uploadCoverImageRest($user)),array('force_canonical' => true));
            //s3
            $coverImage = $this->getS3Url($config['config_ica467']['user_cover_path_upload'].'/'.$this->getUserBusiness()->uploadCoverImageRest($user));
            return $this->success(array('cover_image' => $coverImage));
        }                
        return $this->formInvalid();
        
    }
    
    /**
     * search user
     * 
     * @return
     */
    public function searchAction()
    {  
        $filter = $this->params()->fromQuery('filter',null);
        $userId = $this->identity()->id;
        if(strlen($filter)) {
            $users = $this->getUserDao()->searchUser($filter,array('user_id' => array($userId)));
            $filePaths = array(
                'avatar' => 'user_avatar_path_upload',
                'cover_image' => 'user_cover_path_upload',
            );
            $users = $this->getObjectsUrl($users,$filePaths);
            /*User own sound or in list following: count private sounds*/
            foreach($users as &$user) {
                $userIdFollowings = $this->getFollowingUsers($user['id']);
                if(!in_array($userId,$userIdFollowings)) {
                    $user['sounds'] = (string)($user['sounds']- count($this->getSoundDao()->fetchPrivateSounds($user['id'])));
                }
            }
            return $this->success($users);  
        } else {
            return $this->error('Please enter content to search',400);
        }
    }
    
    public function delete($id) {}
    
    public function getList() {}
}

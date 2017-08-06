<?php
namespace Sound\Controller;

use Api\Controller\AbstractMyRestfulController;
use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractRestfulController;

class SoundRestController extends AbstractMyRestfulController
{  
    protected $soundDao;
    
    protected $userDao;
    
    protected $categoryDao;
    
    protected $soundCategoryDao;
    
    protected $soundBusiness;
    
    public function getSoundBusiness()
    {
        if (!$this->soundBusiness) {
            $sm = $this->getServiceLocator();
            $this->soundBusiness = $sm->get('Sound\Business\SoundBussiness');
        }
        return $this->soundBusiness;    
    }
    
    public function getSoundDao()
    {
        if (!$this->soundDao) {
            $sm = $this->getServiceLocator();
            $this->soundDao = $sm->get('Sound\Model\SoundDao');
        }
        return $this->soundDao;
    }
    
    public function getUserDao()
    {
        if (!$this->userDao) {
            $sm = $this->getServiceLocator();
            $this->userDao = $sm->get('User\Model\UserDao');
        }
        return $this->userDao;
    }
    
    public function getCategoryDao()
    {
        if (!$this->categoryDao) {
            $sm = $this->getServiceLocator();
            $this->categoryDao = $sm->get('User\Model\CategoryDao');
        }
        return $this->categoryDao;
    }
    
    public function getSoundCategoryDao()
    {
        if(!$this->soundCategoryDao) {
            $sm = $this->getServiceLocator();
            $this->soundCategoryDao = $sm->get('User\Model\SoundCategoryDao');
        }
        return $this->soundCategoryDao;
    }

    /**
     * Get list sounds of an user
     * 
     * @return
     */
    public function getList()
    {   
        $my_user_id = $this->identity()->id;
        $user_id = $this->params()->fromQuery('user_id');
        $limit = $this->params()->fromQuery('limit',null);
        $offset = $this->params()->fromQuery('offset',null);
        $updated_at = $this->params()->fromQuery('updated_at',null);
        /*User own sound or in list following can see all sound(private and public)*/
        $type = \Sound\Model\Dto\SoundDto::SOUND_TYPE_BROADCAST;
        if($my_user_id == $user_id || in_array($my_user_id,$this->getFollowingUsers($user_id))) {
            $type = null;
        }
        
        if(!$this->getUserDao()->fetchOne($user_id)) {
            return $this->error('The user you request does not exist');    
        }
        
        $criteria = array(
            'user_id' => $user_id,
            'type' => $type,
            'limit' => $limit,
            'offset' => $offset,
            'updated_at' => $updated_at,
        );
        $sounds = $this->getSoundDao()->getList($criteria);
        $filePaths = array(
            'thumbnail' => 'sound_thumbnail_path_upload',
            'thumbnail2' => 'sound_thumbnail2_path_upload',
            'thumbnail3' => 'sound_thumbnail3_path_upload',
            'sound_path' => 'sound_file_path_upload',
        );
        $sounds = $this->getObjectsUrl($sounds,$filePaths);
        
        return $this->success($sounds);
        
    }

    /**
     * Get details a sound
     * 
     * @param mixed $id
     * @return
     */
    public function get($id)
    {  
        $soundId = (int) $id;        
        if(!$this->getSoundDao()->fetchOne($soundId)) {
            return $this->error('The sound you request does not exist');
        }
        
        $userId = $this->identity()->id;
        $sound = $this->getSoundDao()->fetchOneDetail($soundId);
        /*User own sound or in list following can see all sound(private and public)*/
        $userPermission = false;
        if($userId == $sound['user_id'] || in_array($userId,$this->getFollowingUsers($sound['user_id']))) {
            $userPermission = true;
        }
        if($sound['type'] == \Sound\Model\Dto\SoundDto::SOUND_TYPE_PENDING && $userPermission == false) {
            return $this->error('Only owner sound can get private sound',400);    
        }
        
        $filePaths = array(
            'thumbnail' => 'sound_thumbnail_path_upload',
            'thumbnail2' => 'sound_thumbnail2_path_upload',
            'thumbnail3' => 'sound_thumbnail3_path_upload',
            'sound_path' => 'sound_file_path_upload',
            'avatar' => 'user_avatar_path_upload',
        );
        $sound = $this->getObjectsUrl($sound,$filePaths,'one');
        
        $params = $this->params()->fromRoute();
        if(isset($params['sub_route']) and $params['sub_route'] == 'subinfo') {
            $categories = $this->getSoundCategoryDao()->fetchAllBy('sound_id',$soundId);
            $categoriesId = array();
            foreach($categories as $category) {
                $categoriesId[] = $category->category_id;
            }
            return $this->success(array(
                'type' => $sound->type,
                'connect_facebook' => $sound->connect_facebook,
                'connect_twitter' => $sound->connect_twitter,
                'tags' => $sound->tags,
                'category_id' => implode(',',$categoriesId),
            ));            
        }
        return $this->success($sound);
        
    }

    /**
     * Create/Upload a new sound
     * 
     * @param mixed $data
     * @return
     */
    public function create($data)
    { 
        $params = $this->params()->fromRoute();
        if(isset($params['sub_route']) and $params['sub_route'] == 'update') {            
           return $this->updateSound();            
        }
        
        $data['user_id'] = $this->identity()->id;
        $form = $this->getSoundForm();
        $formFilter = new \Sound\Form\SoundFormFilter;
        $formFilter->setServiceLocator($this->getServiceLocator());
        $form->setInputFilter($formFilter->getInputFilter());
        $data = array_merge($data,$_FILES);
        $form->setData($data);
        if($form->isValid()) {
            $soundDto = new \Sound\Model\Dto\SoundDto;
            $soundDto->exchangeArray($form->getData());
            $soundDto->category_id = $data['category_id'];
            $soundId = $this->getSoundBusiness()->createSoundRest($soundDto);
            return $this->get($soundId);
        } else {
            return $this->formInvalid();
        }   
    }
    
    /**
     * Update a sound
     * 
     * @return
     */
    public function updateSound()
    { 
        $data = $this->getRequest()->getPost()->toArray();
        
        $soundId = $this->params()->fromRoute('id',0);
        $sound = $this->getSoundDao()->fetchOne($soundId);
        if(!$sound) {
            return $this->error('The sound you request does not exist');
        }
        $oldThumbnail = $sound->thumbnail;
        $oldThumbnail2 = $sound->thumbnail2;
        $oldThumbnail3 = $sound->thumbnail3;
        $oldSoundPath = $sound->sound_path;
        
        if($sound->user_id != $this->identity()->id) {
            return $this->error('This sound does not belong to you so you can not edit it',400);    
        }
        
        $form = $this->getSoundForm();
        $form->bind($sound);
        $formFilter = new \Sound\Form\SoundFormFilter;
        $formFilter->setServiceLocator($this->getServiceLocator());
        $formFilter->getInputFilter()->get('sound_path')->setAllowEmpty(true); 
        $form->setInputFilter($formFilter->getInputFilter());
        $data['user_id'] = $this->identity()->id;
        $data = array_merge($data,$_FILES);
        $form->setData($data);
        if($form->isValid()) {
            $sound->old_thumbnail = $oldThumbnail;
            $sound->old_thumbnail2 = $oldThumbnail2;
            $sound->old_thumbnail3 = $oldThumbnail3;
            $sound->old_sound_path = $oldSoundPath;
            $sound->category_id = (!empty($data['category_id'])) ? $data['category_id'] : null;
            $this->getSoundBusiness()->updateSoundRest($sound);
            
            return $this->get($soundId);
        } else {
            return $this->formInvalid();    
        }
    }
    
    public function getSoundForm()
    {
        $users = $this->getUserDao()->fetchAll();
        $users_array = array();
        foreach($users as $user) {
            $users_array[$user->id] = $user->display_name;
        }
        
        $categories = $this->getCategoryDao()->fetchAll();
        $categories_array = array();
        foreach($categories as $category) {
            $categories_array[$category->id] = $category->name;
        }
        
        $form = new \Sound\Form\SoundForm;
        $form->get('user_id')->setOptions(array('value_options' => $users_array));
        $form->get('category_id')->setOptions(array('value_options' => $categories_array));
        
        return $form;
    }
    
    /**
     * Search sounds by title
     * 
     * @return
     */
    public function searchAction()
    {  
        $filter = $this->params()->fromQuery('filter',null);
        if(strlen($filter)) {
            $sounds = $this->getSoundDao()->searchSound($filter);
            $filePaths = array(
                'thumbnail' => 'sound_thumbnail_path_upload',
                'thumbnail2' => 'sound_thumbnail2_path_upload',
                'thumbnail3' => 'sound_thumbnail3_path_upload',
                'sound_path' => 'sound_file_path_upload',
                'avatar' => 'user_avatar_path_upload',
            ); 
            $sounds = $this->getObjectsUrl($sounds,$filePaths);
            $userId = $this->identity()->id;
            foreach($sounds as $key => $sound) {
                /*User own sound or in list following can see all sound(private and public)*/
                $userPermission = false;
                if($userId == $sound['user_id'] || in_array($userId,$this->getFollowingUsers($sound['user_id']))) {
                    $userPermission = true;
                }
                if($sound['type'] == \Sound\Model\Dto\SoundDto::SOUND_TYPE_PENDING && $userPermission == false) {
                    unset($sounds[$key]);
                }
            }
            return $this->success($sounds);
        } else {
            return $this->error('Please enter content to search',400);
        }
    }
    
    /**
     * Search all tags
     * 
     * @return
     */
    public function searchByTagAction()
    {
        $filter = $this->params()->fromQuery('filter',null);
        $tags = $this->getSoundBusiness()->getTags($filter);
        return $this->success($tags);
    }
    
    /**
     * Search all sounds with a specific tag
     * 
     * @return
     */
    public function searchSoundByTagAction()
    {
        $filter = $this->params()->fromQuery('filter',null);
        if(strlen($filter)) {
            $sounds = $this->getSoundDao()->searchSound($filter,'tag');
            $filePaths = array(
                'thumbnail' => 'sound_thumbnail_path_upload',
                'thumbnail2' => 'sound_thumbnail2_path_upload',
                'thumbnail3' => 'sound_thumbnail3_path_upload',
                'sound_path' => 'sound_file_path_upload',
                'avatar' => 'user_avatar_path_upload',
            ); 
            $sounds = $this->getObjectsUrl($sounds,$filePaths);
            $userId = $this->identity()->id;
            foreach($sounds as $key => $sound) {
                /*User own sound or in list following can see all sound(private and public)*/
                $userPermission = false;
                if($userId == $sound['user_id'] || in_array($userId,$this->getFollowingUsers($sound['user_id']))) {
                    $userPermission = true;
                }
                if($sound['type'] == \Sound\Model\Dto\SoundDto::SOUND_TYPE_PENDING && $userPermission == false) {
                    unset($sounds[$key]);
                }
            }
            return $this->success($sounds);
        } else {
            return $this->error('Please enter content to search',400);
        }
    }
    
    
    /**
     * Search all: user, sound, tag
     * 
     * @return
     */
    public function searchAllAction()
    {
        $filter = $this->params()->fromQuery('filter',null);
        if($filter) {
            $tags = $this->getSoundBusiness()->getTags($filter);
            
            $sounds = $this->getSoundDao()->searchSound($filter);
            $filePaths = array(
                'thumbnail' => 'sound_thumbnail_path_upload',
                'thumbnail2' => 'sound_thumbnail2_path_upload',
                'thumbnail3' => 'sound_thumbnail3_path_upload',
                'sound_path' => 'sound_file_path_upload',
                'avatar' => 'user_avatar_path_upload',
            ); 
            $sounds = $this->getObjectsUrl($sounds,$filePaths);
            $userId = $this->identity()->id;
            foreach($sounds as $key => $sound) {
                /*User own sound or in list following can see all sound(private and public)*/
                $userPermission = false;
                if($userId == $sound['user_id'] || in_array($userId,$this->getFollowingUsers($sound['user_id']))) {
                    $userPermission = true;
                }
                if($sound['type'] == \Sound\Model\Dto\SoundDto::SOUND_TYPE_PENDING && $userPermission == false) {
                    unset($sounds[$key]);
                }
            }
            
            $users = $this->getUserDao()->searchUser($filter);
            $filePathUsers = array(
                'avatar' => 'user_avatar_path_upload',
                'cover_image' => 'user_cover_path_upload',
            );
            $users = $this->getObjectsUrl($users,$filePathUsers);
            /*User own sound or in list following: count private sounds*/
            foreach($users as &$user) {
                $userIdFollowings = $this->getFollowingUsers($user['id']);
                if(!in_array($userId,$userIdFollowings)) {
                    $user['sounds'] = (string)($user['sounds']- count($this->getSoundDao()->fetchPrivateSounds($user['id'])));
                }
            }
            
            return $this->success(array(
                'users' => $users,
                'sounds' => $sounds,
                'tags' => $tags, 
            ));
        } else {
            return $this->error('Please enter content to search',400);   
        }
    }
    
    /**
     * Delete a sound
     * 
     * @param mixed $id
     * @return
     */
    public function delete($id)
    {        
        $sound = $this->getSoundDao()->fetchOne($id);
        $userId = $this->identity()->id;
        if(!$sound) {
            return $this->error('The sound you request does not exist');
        }
        if($sound->user_id != $userId) {
            return $this->error('This sound does not belong to you so you can not delete it',400);
        }
        $this->getSoundBusiness()->deleteSoundRest($sound);
        return $this->success();
       
    }
    
    /**
     * Update a sound when people click play
     * 
     * @param mixed $id
     * @param mixed $data
     * @return
     */
    public function update($id, $data)
    {
        $params = $this->params()->fromRoute();
        $sound = $this->getSoundDao()->fetchOne($id);
        if(!$sound) {
            return $this->error('The sound you request does not exist');
        }
//        if(isset($params['sub_route']) and $params['sub_route'] == 'view') { 
//           $sound->viewed = (int)$sound->viewed + 1;
//           $this->getSoundDao()->save($sound);
//           return $this->success();
//                      
//        }
//        if(isset($params['sub_route']) and $params['sub_route'] == 'play') {
//           $sound->played = (int)$sound->played + 1;
//           $this->getSoundDao()->save($sound);
//           return $this->success();            
//        }
        die;
    }
    
}

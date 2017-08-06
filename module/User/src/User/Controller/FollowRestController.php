<?php
namespace User\Controller;

use Api\Controller\AbstractMyRestfulController;
use Zend\View\Model\JsonModel;

class FollowRestController extends AbstractMyRestfulController
{  
    protected $followDao;
    
    protected $userDao;
    
    protected $settingsBusiness;
    
    public function getFollowDao()
    {
        if (!$this->followDao) {
            $sm = $this->getServiceLocator();
            $this->followDao = $sm->get('User\Model\FollowAudienceDao');
        }
        return $this->followDao;
    }
    
    public function getUserDao()
    {
        if (!$this->userDao) {
            $sm = $this->getServiceLocator();
            $this->userDao = $sm->get('User\Model\UserDao');
        }
        return $this->userDao;
    }
    
    public function getSettingsBusiness()
    {
        if(!$this->settingsBusiness) {
            $sm = $this->getServiceLocator();
            $this->settingsBusiness = $sm->get('Settings\Business\SettingsBussiness');
        }
        return $this->settingsBusiness;
    }
    
    /**
     * Get list users following of an user
     * 
     * @return
     */
    public function get($id)
    {  
        $userIdFollowing = (int)$id;
        if(!$this->getUserDao()->fetchOne($userIdFollowing)) {
            return $this->error('The user id you request does not exist');
        }
        $limit = $this->params()->fromQuery('limit',null);
        $offset = $this->params()->fromQuery('offset',null);
        $updated_at = $this->params()->fromQuery('updated_at',null);
        
        $criteria = array(
            'user' => $userIdFollowing,
            'limit' => $limit,
            'offset' => $offset,
            'updated_at' => $updated_at,
        );
        
        $listUsersFollowing = $this->getFollowDao()->fetchUsersFollowing($criteria);
        $filePaths = array(
            'avatar' => 'user_avatar_path_upload',
            'cover_image' => 'user_cover_path_upload',
        );
        $listUsersFollowing = $this->getObjectsUrl($listUsersFollowing,$filePaths);
        
        return $this->success($listUsersFollowing);
    }

    /**
     * Follow someone
     * 
     * @param mixed $data
     * @return
     */
    public function create($data)
    {  
        $user_id_audience = (int) $data['user_id_audience'];
        $user_id_following = $this->identity()->id;
        
        if(!$this->getUserDao()->fetchOne($user_id_audience)) {
            return $this->error('The user you want to follow does not exist',400);
        }
        
        if($user_id_following == $user_id_audience) {
            return $this->error('You can not follow yourself',400);
        }
        if($this->getFollowDao()->fetchOne($user_id_audience,$user_id_following)) {
            return $this->error('You have followed him or her already',400);    
        }
        
        $data['user_id_following'] = $user_id_following;
        
        $form = new \User\Form\FollowForm;
        $formFilter = new \User\Form\FollowFormFilter;
        $form->setInputFilter($formFilter->getInputFilter());
        $form->setData($data);
        
        if($form->isValid()) {
            $followDto = new \User\Model\Dto\FollowAudienceDto;
            $followDto->exchangeArray($form->getData());
            $this->getFollowDao()->save($followDto);
            $this->getSettingsBusiness()->push(array(
                'type' => \Settings\Model\Dto\SettingsDto::PUSH_FOLLOW,
                'id' => $user_id_audience,
                'user_id' => $user_id_following,
            ));
            
            return $this->success();
        } else {
            return $this->formInvalid();
        }

    }

    /**
     * Turn off follow someone
     * 
     * @param mixed $id
     * @return
     */
    public function delete($id)
    {
        $user_id_audience = (int) $id;
        $user_id_following = $this->identity()->id;
        
        if(!$this->getUserDao()->fetchOne($user_id_audience)) {
            return $this->error('The user you want to turn-off follow does not exist',400);
        }
        
        if($user_id_following == $user_id_audience) {
            return $this->error('You can not turn-off follow yourself',400);
        }
        $follow = $this->getFollowDao()->fetchOne($user_id_audience,$user_id_following);
        if(!$follow) {
            return $this->error('You have not yet followed him or her so you can not turn-off follow',400);    
        }
        
        $this->getFollowDao()->delete($follow->id);
        return $this->success();
            
    }
    
    /**
     * Get my following users
     * 
     * @return
     */
    public function getList()
    {
        $userIdFollowing = $this->identity()->id;
        $limit = $this->params()->fromQuery('limit',null);
        $offset = $this->params()->fromQuery('offset',null);
        $updated_at = $this->params()->fromQuery('updated_at',null);
        
        $criteria = array(
            'user' => $userIdFollowing,
            'limit' => $limit,
            'offset' => $offset,
            'updated_at' => $updated_at,
        );
        
        $listUsersFollowing = $this->getFollowDao()->fetchUsersFollowing($criteria);
        $filePaths = array(
            'avatar' => 'user_avatar_path_upload',
            'cover_image' => 'user_cover_path_upload',
        );
        $listUsersFollowing = $this->getObjectsUrl($listUsersFollowing,$filePaths);
        
        return $this->success($listUsersFollowing);
    }
    
    public function update($id, $data) {}
    

}

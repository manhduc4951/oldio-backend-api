<?php
namespace User\Controller;

use Api\Controller\AbstractMyRestfulController;
use Zend\View\Model\JsonModel;

class AudienceRestController extends AbstractMyRestfulController
{  
    protected $audienceDao;
    
    protected $userDao;
    
    protected $soundDao;
    
    public function getAudienceDao()
    {
        if (!$this->audienceDao) {
            $sm = $this->getServiceLocator();
            $this->audienceDao = $sm->get('User\Model\FollowAudienceDao');
        }
        return $this->audienceDao;
    }
    
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
    
    /**
     * Get list audiences of an user
     * 
     * @param mixed $id
     * @return void
     */
    public function get($id)
    {
        $userIdAudience = (int)$id;
        if(!$this->getUserDao()->fetchOne($userIdAudience)) {
            return $this->error('The user id you request does not exist');
        }
        $limit = $this->params()->fromQuery('limit',null);
        $offset = $this->params()->fromQuery('offset',null);
        $updated_at = $this->params()->fromQuery('updated_at',null);
        
        $criteria = array(
            'user' => $userIdAudience,
            'limit' => $limit,
            'offset' => $offset,
            'updated_at' => $updated_at,
        );
        
        $listUsersAudience = $this->getAudienceDao()->fetchUsersAudience($criteria);
        $filePaths = array(
            'avatar' => 'user_avatar_path_upload',
            'cover_image' => 'user_cover_path_upload', 
        );
        $listUsersAudience = $this->getObjectsUrl($listUsersAudience,$filePaths);
        /*User in list following: count private sounds*/
        foreach($listUsersAudience as &$user) {
            $userIdFollowings = $this->getFollowingUsers($user['id']);
            if(!in_array($userIdAudience,$userIdFollowings)) {
                $user['sounds'] = (string)($user['sounds']- count($this->getSoundDao()->fetchPrivateSounds($user['id'])));
            }
        }
        
        return $this->success($listUsersAudience);
    }
    
    /**
     * Turn off audience
     * 
     * @param mixed $id
     * @return
     */
    public function delete($id)
    {
        $userIdAudience = $this->identity()->id;
        $userIdFollowing = $id;
        
        if(!$this->getUserDao()->fetchOne($userIdFollowing)) {
            return $this->error('The user you want to turn-off audience does not exist',400);
        }
        if($userIdAudience == $userIdFollowing) {
            return $this->error('You can not turn-off audience yourself',400);
        }
        $follow = $this->getAudienceDao()->fetchOne($userIdAudience,$userIdFollowing);
        if(!$follow) {
            return $this->error('This user has not yet followed you so you can not turn-off audience',400);    
        }
        $this->getAudienceDao()->delete($follow->id);
        return $this->success();
    }
    
    /**
     * Get my audience users
     * 
     * @return
     */
    public function getList()
    {
        $userIdAudience = $this->identity()->id;
        $limit = $this->params()->fromQuery('limit',null);
        $offset = $this->params()->fromQuery('offset',null);
        $updated_at = $this->params()->fromQuery('updated_at',null);
        
        $criteria = array(
            'user' => $userIdAudience,
            'limit' => $limit,
            'offset' => $offset,
            'updated_at' => $updated_at,
        );
        
        $listUsersAudience = $this->getAudienceDao()->fetchUsersAudience($criteria);
        $filePaths = array(
            'avatar' => 'user_avatar_path_upload',
            'cover_image' => 'user_cover_path_upload', 
        );
        $listUsersAudience = $this->getObjectsUrl($listUsersAudience,$filePaths);
        /*User in list following: count private sounds*/
        foreach($listUsersAudience as &$user) {
            $userIdFollowings = $this->getFollowingUsers($user['id']);
            if(!in_array($userIdAudience,$userIdFollowings)) {
                $user['sounds'] = (string)($user['sounds']- count($this->getSoundDao()->fetchPrivateSounds($user['id'])));
            }
        }
        
        return $this->success($listUsersAudience);
    }
    
    public function create($data) {}
    
    public function update($id, $data) {}
    
}

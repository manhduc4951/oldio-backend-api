<?php
namespace User\Controller;

use Api\Controller\AbstractMyRestfulController;
use Zend\View\Model\JsonModel;

class HomeRestController extends AbstractMyRestfulController
{
    
    protected $followDao;
    
    protected $soundDao;
    
    public function getFollowDao()
    {
        if (!$this->followDao) {
            $sm = $this->getServiceLocator();
            $this->followDao = $sm->get('User\Model\FollowAudienceDao');
        }
        return $this->followDao;
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
     * Get news feed from following list (home screen)
     * 
     * @return
     */
    public function getList()
    {
        $userIdFollowing = $this->identity()->id;
        $limit = $this->params()->fromQuery('limit',null);
        $offset = $this->params()->fromQuery('offset',null);
        $timeFrom = $this->params()->fromQuery('time_from',null);
        $timeTo = $this->params()->fromQuery('time_to',null);
        
        $userIds = array();
        foreach($this->getFollowDao()->fetchUsersFollowing(array('user' => $userIdFollowing),'minimum') as $follow) {
            $userIds[] = $follow->user_id_audience;
        }
        $userIds[] = $userIdFollowing;
        
        $criteria = array(
            'users' => $userIds,
            'limit' => $limit,
            'offset' => $offset,
            'time_from' => $timeFrom,
            'time_to' => $timeTo,
        );
        
        $sounds = $this->getSoundDao()->getNewsFeed($criteria);
        $filePaths = array(
            'avatar' => 'user_avatar_path_upload',
            'thumbnail' => 'sound_thumbnail_path_upload',
            'thumbnail2' => 'sound_thumbnail2_path_upload',
            'thumbnail3' => 'sound_thumbnail3_path_upload',
            'sound_path' => 'sound_file_path_upload',       
        );
        $sounds = $this->getObjectsUrl($sounds,$filePaths);
        foreach($sounds as $key => $sound) {
            /*User own sound or in list following can see all sound(private and public)*/
            $userPermission = false;
            if($userIdFollowing == $sound['user_id'] || in_array($userIdFollowing,$this->getFollowingUsers($sound['user_id']))) {
                $userPermission = true;
            }
            if($sound['type'] == \Sound\Model\Dto\SoundDto::SOUND_TYPE_PENDING && $userPermission == false) {
                unset($sounds[$key]);
            }
              
        }
        
        return $this->success($sounds);
    }

    public function create($data) {}
    
    public function delete($id) {}
    
    public function update($id, $data) {}
    
    public function get($id) {}

}

<?php
namespace Application\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class GetFollowingUsers extends AbstractPlugin
{
    protected $serviceLocator;
    
    protected $followDao;
    
    public function getFollowDao()
    {
        if(!$this->followDao) {
            $this->followDao = $this->getServiceLocator()->get('User\Model\FollowAudienceDao');
        }
        return $this->followDao;
    }
    
    public function __construct($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
    
    /**
     * Get all users are following a specific user
     * 
     * @param mixed $userId
     * @return
     */
    public function __invoke($userId)
    {
        $userFollowings = $this->getFollowDao()->fetchAllBy('user_id_audience',$userId);
        if($userFollowings) {
            $users = array();
            foreach($userFollowings as $userFollowing) {
                $users[] = $userFollowing->user_id_following;
            }
        }
        
        return $users;
    }
    
}
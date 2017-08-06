<?php
namespace Settings\Controller;

use Api\Controller\AbstractMyRestfulController;
use Zend\View\Model\JsonModel;

class NotificationRestController extends AbstractMyRestfulController
{
    protected $notificationDao;
    
    public function getNotificationDao()
    {
        if(!$this->notificationDao) {
            $sm = $this->getServiceLocator();
            $this->notificationDao = $sm->get('Settings\Model\NotificationDao');
        }
        return $this->notificationDao;
    }
    
    /**
     * Get notifications of an user
     * 
     * @param mixed $id
     * @return
     */
    public function get($id)
    {
        $limit = $this->params()->fromQuery('limit',null);
        $offset = $this->params()->fromQuery('offset',null);
        $timeFrom = $this->params()->fromQuery('time_from',null);
        $timeTo = $this->params()->fromQuery('time_to',null);
        
        $criteria = array(
            'my_user_id' => $id,
            'limit' => $limit,
            'offset' => $offset,
            'time_from' => $timeFrom,
            'time_to' => $timeTo,
        );
        
        $notifications = $this->getNotificationDao()->fetchAll($criteria);
        $filePaths = array(
            'avatar' => 'user_avatar_path_upload',
        );
        $notifications = $this->getObjectsUrl($notifications,$filePaths);
        
        return $this->success($notifications);
    }
    
    /**
     * Read a notification
     * 
     * @param mixed $id
     * @param mixed $data
     * @return
     */
    public function update($id, $data)
    {
        $notification = $this->getNotificationDao()->fetchOne($id);
        if(!$notification) {
            return $this->error('The notification you request does not exist');
        }
        $notification->read = 1;
        $this->getNotificationDao()->update($notification);
        
        return $this->success();
    }
    
    /**
     * Set all notifications of an user to read
     * 
     * @return void
     */
    public function getList()
    {
        $userId = $this->identity()->id;
        $this->getNotificationDao()->setAllToRead($userId);
        
        return $this->success();
    }
    
    public function create($data) {}

    public function delete($id) {}
    
    
}

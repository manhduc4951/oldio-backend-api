<?php
namespace Settings\Controller;

use Api\Controller\AbstractMyRestfulController;
use Zend\View\Model\JsonModel;

class DeviceTokenRestController extends AbstractMyRestfulController
{  
    private $deviceTokenDao;
    
    public function getDeviceTokenDao()
    {
        if(!$this->deviceTokenDao) {
            $sm = $this->getServiceLocator();
            $this->deviceTokenDao = $sm->get('Settings\Model\DeviceTokenDao');
        }
        return $this->deviceTokenDao;
    }

    public function getList() {}
    

    public function get($id) {}
    

    /**
     * Insert new device token
     * 
     * @param mixed $data
     * @return
     */
    public function create($data)
    {
        $userId = $this->identity()->id;
        if($this->getDeviceTokenDao()->fetchOne($userId,$data['device_token'])) {
            return $this->error('The device token of your user has already exist',400);
        }
        
        $deviceToken = new \Settings\Model\Dto\DeviceTokenDto;
        $deviceToken->exchangeArray($data);
        $deviceToken->user_id = $userId;
        $this->getDeviceTokenDao()->save($deviceToken);
        return $this->success();
    }
    

    public function update($id, $data) {}
    

    /**
     * Delete device token
     * 
     * @param mixed $deviceToken
     * @return
     */
    public function delete($deviceToken)
    {
        $this->getDeviceTokenDao()->delete($this->identity()->id,$deviceToken);
        return $this->success();
    }
    
}

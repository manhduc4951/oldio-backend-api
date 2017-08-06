<?php
namespace Settings\Controller;

use Api\Controller\AbstractMyRestfulController;
use Zend\View\Model\JsonModel;

class AppConfigRestController extends AbstractMyRestfulController
{  
    protected $appConfigDao;
    
    /*Test*/
    protected $settingBusiness;
    
    protected $notificationDao;
    
    public function getSettingBusiness()
    {
        if(!$this->settingBusiness) {
            $this->settingBusiness = $this->getServiceLocator()->get('Settings\Business\SettingsBussiness');
        }
        return $this->settingBusiness;
    }
    
    public function getNotificationDao()
    {
        if(!$this->notificationDao) {
            $sm = $this->getServiceLocator();
            $this->notificationDao = $sm->get('Settings\Model\NotificationDao');
        }
        return $this->notificationDao;
    }
    /*Test*/
    
    public function getAppConfigDao()
    {
        if (!$this->appConfigDao) {
            $sm = $this->getServiceLocator();
            $this->appConfigDao = $sm->get('Settings\Model\AppConfigDao');
        }
        return $this->appConfigDao;
    }


    public function getList()
    {  
        $appConfig = $this->getAppConfigDao()->fetchAll()->toArray();
        
        return $this->success($appConfig);
    }

    /**
     * Using for testing
     * 
     * @param mixed $id
     * @return void
     */
    public function get($id)
    {
        file_put_contents('public/log.txt', "dauma".PHP_EOL,FILE_APPEND);
        die;
        echo '<pre>'; var_dump(count($this->getNotificationDao()->fetchAllUnreadNotification(85))); echo '</pre>'; die;
        $data = array(
            'device_token' => '69f9a91057589dd6e183982c1f377e78e61cf85bc4e82223777d72fcaf138561',
            'alert' => 'testing',
            'badge' => 999,
        );
        $this->getSettingBusiness()->pushNotification($data);
    }
    

    public function create($data) {}
    

    public function update($id, $data) {}
    

    public function delete($id) {}
    
}

<?php
namespace Settings\Controller;

use Api\Controller\AbstractMyRestfulController;
use Zend\View\Model\JsonModel;

class SettingsRestController extends AbstractMyRestfulController
{
    protected $settingsDao;
    
    public function getSettingsDao()
    {
        if(!$this->settingsDao) {
            $sm = $this->getServiceLocator();
            $this->settingsDao = $sm->get('Settings\Model\SettingsDao');
        }
        return $this->settingsDao;
    }
    
    /**
     * Get settings of an user
     * 
     * @return
     */
    public function getList()
    {  
        $userId = $this->identity()->id;
        $settings = $this->getSettingsDao()->fetchOneBy('user_id',$userId);
        
        return $this->success($settings);
        
    }
    
    /**
     * Receive request
     * 
     * @param mixed $data
     * @return
     */
    public function replaceList($data)
    {  
        $params = $this->params()->fromRoute();
        $settings = $this->getSettingsDao()->fetchOneBy('user_id',$this->identity()->id);
        if(isset($params['sub_route']) && $params['sub_route'] == 'connection') {
            return $this->updateConnection($data,$settings);
        }
        if(isset($params['sub_route']) && $params['sub_route'] == 'sound-quality') {
            return $this->updateSoundQuality($data,$settings);
        }
        if(isset($params['sub_route']) && $params['sub_route'] == 'notification') {
            return $this->updateNotification($data,$settings);
        }
        die('nothing to do here');
    }
    
    /**
     * Update connection(facebook,twitter)
     * 
     * @param mixed $data
     * @param mixed $settings
     * @return
     */
    public function updateConnection($data,$settings)
    {  
        $form = new \Settings\Form\ConnectionForm;
        $form->bind($settings);
        $formFilter = new \Settings\Form\ConnectionFormFilter;
        $form->setInputFilter($formFilter->getInputFilter());
        $form->setData($data);
        if($form->isValid()) {
            $this->getSettingsDao()->save($settings);
            return $this->success();
        } else {
            return $this->formInvalid();
        }
        
    }
    
    /**
     * Update sound quality
     * 
     * @param mixed $data
     * @param mixed $settings
     * @return
     */
    public function updateSoundQuality($data,$settings)
    {
        $form = new \Settings\Form\SoundQualityForm;
        $form->bind($settings);
        $formFilter = new \Settings\Form\SoundQualityFormFilter;
        $form->setInputFilter($form->getInputFilter());
        $form->setData($data);
        if($form->isValid()) {
            $this->getSettingsDao()->save($settings);
            return $this->success();    
        } else {
            return $this->formInvalid();
        }
    }
    
//    public function updateNotification($data,$settings)
//    {
//        $form = new \Settings\Form\NotificationForm;
//        $form->bind($settings);
//        $formFilter = new \Settings\Form\NotificationFormFilter;
//        $form->setInputFilter($formFilter->getInputFilter());
//        $form->setData($data);
//        if($form->isValid()) {
//            $this->getSettingsDao()->save($settings);
//            return $this->success();
//        } else {
//            return $this->formInvalid();
//        }
//    }
    
    /**
     * Update notification(email and push)
     * 
     * @param mixed $data
     * @param mixed $settings
     * @return
     */
    public function updateNotification($data,$settings)
    {
        $notificationName = array(
            'email_follow_me',
            'email_comments_on_my_post',
            'email_like_my_sound',
            'email_comments_on_a_post_i_care',
            'push_follow_me',
            'push_comments_on_my_post',
            'push_like_my_sound',
            'push_comments_on_a_post_i_care',
        );
        $notificationValue = explode(',',$data['notification']);
        if(count($notificationName) != count($notificationValue)) {
            return $this->formInvalid();
        }
        $notification = array_combine($notificationName,$notificationValue);
        $settings->exchangeArray($notification);
        $this->getSettingsDao()->save($settings);
        return $this->success();
    }
   
    public function get($id) {}
    
    public function create($data) {}

    public function update($id, $data) {}

    public function delete($id) {}
    
}

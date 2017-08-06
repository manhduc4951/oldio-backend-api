<?php
namespace Sound\Controller;

use Api\Controller\AbstractMyRestfulController;
use Zend\View\Model\JsonModel;

class ViewRestController extends AbstractMyRestfulController
{
    protected $viewDao;
    
    protected $soundDao;
    
    public function getViewDao()
    {
        if(!$this->viewDao) {
            $sm = $this->getServiceLocator();
            $this->viewDao = $sm->get('Sound\Model\ViewDao');
        }
        return $this->viewDao;
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
     * View a sound
     * 
     * @param mixed $data
     * @return
     */
    public function create($data)
    {
        $userId = $this->identity()->id;
        $soundId = $data['sound_id'];
        if (!$this->getSoundDao()->fetchOne($soundId)) {
            return $this->error('The sound you request does not exist !');     
        }
        if($this->getViewDao()->fetchOne($soundId,$userId)) {
            return $this->error('You have already viewed this sound',400);     
        }
        $view = new \Sound\Model\Dto\ViewDto;
        $view->sound_id = $soundId;
        $view->user_id = $userId;
        $this->getViewDao()->save($view);
        
        return $this->success();         
    }
    
    public function delete($id) {}
    
    public function getList() {}

    public function update($id, $data) {}

    public function get($id) {}
    
}

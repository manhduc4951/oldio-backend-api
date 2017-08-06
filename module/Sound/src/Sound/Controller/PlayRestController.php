<?php
namespace Sound\Controller;

use Api\Controller\AbstractMyRestfulController;
use Zend\View\Model\JsonModel;

class PlayRestController extends AbstractMyRestfulController
{
    protected $playDao;
    
    protected $soundDao;
    
    public function getPlayDao()
    {
        if(!$this->playDao) {
            $sm = $this->getServiceLocator();
            $this->playDao = $sm->get('Sound\Model\PlayDao');
        }
        return $this->playDao;
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
     * Play a sound
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
        if($this->getPlayDao()->fetchOne($soundId,$userId)) {
            return $this->error('You have already played this sound',400);     
        }
        $play = new \Sound\Model\Dto\PlayDto;
        $play->sound_id = $soundId;
        $play->user_id = $userId;
        $this->getPlayDao()->save($play);
        
        return $this->success();         
    }
    
    public function delete($id) {}
    
    public function getList() {}

    public function update($id, $data) {}

    public function get($id) {}
    
}

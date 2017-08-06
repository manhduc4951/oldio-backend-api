<?php
namespace SoundSet\Controller;

use Api\Controller\AbstractMyRestfulController;
use Zend\View\Model\JsonModel;

class UserSoundSetRestController extends AbstractMyRestfulController
{  
    protected $userSoundSetDao;
    
    protected $soundSetDao;
    
    protected $soundSetBusiness;
    
    public function getSoundSetBusiness()
    {
        if(!$this->soundSetBusiness) {
            $sm = $this->getServiceLocator();
            $this->soundSetBusiness = $sm->get('SoundSet\Business\SoundSetBussiness');
        }
        return $this->soundSetBusiness;
    }
    
    public function getUserSoundSetDao()
    {
        if(!$this->userSoundSetDao) {
            $sm = $this->getServiceLocator();
            $this->userSoundSetDao = $sm->get('SoundSet\Model\UserSoundSetDao');
        }
        return $this->userSoundSetDao;
    }
    
    public function getSoundSetDao()
    {
        if(!$this->soundSetDao) {
            $sm = $this->getServiceLocator();
            $this->soundSetDao = $sm->get('SoundSet\Model\SoundSetDao');
        }
        return $this->soundSetDao;
    }

    /**
     * Get list sound set of an user (Sound board)
     * 
     * @return
     */
    public function getList()
    {  
        $userId = $this->identity()->id;
        $criteria = array(
            'user_id' => $userId,
        );
        $soundsSet = $this->getUserSoundSetDao()->fetchAll($criteria);
        $filePaths = array(
            'image' => 'sound_set_image_upload_s3',
        );
        $soundsSet = $this->getObjectsUrl($soundsSet,$filePaths);
        
        return $this->success($soundsSet);
            
    }
    
    /**
     * Delete sound set from sound board of an user
     * 
     * @param mixed $id
     * @return
     */
    public function delete($id)
    {
        $userId = $this->identity()->id;
        $soundSetId = (int) $id;
        $this->getUserSoundSetDao()->delete($soundSetId,$userId);
        
        return $this->success();
    }
    
    /**
     * Purchase sound set
     * 
     * @param mixed $data
     * @return void
     */
    public function create($data)
    {  
        $userId = $this->identity()->id;
        $soundSetId = (int) $data['sound_set_id'];
        $soundSet = $this->getSoundSetDao()->fetchOne($soundSetId);
        
        if(!$soundSet) {
            return $this->error('The sound set you request does not exits');
        }
        if($this->getUserSoundSetDao()->fetchOne($soundSetId,$userId)) {
            return $this->error('You have bought this sound set already',400);
        }
        
        $userSoundSet = new \SoundSet\Model\Dto\UserSoundSetDto;
        $userSoundSet->exchangeArray($data);
        $userSoundSet->user_id = $userId;
        $userSoundSet->created_at = date('Y-m-d H:i:s');
        $this->getUserSoundSetDao()->save($userSoundSet);
        
        $soundSet = $this->getUserSoundSetDao()->fetchOneUserSoundSet(array(
            'user_id' => $userId,
            'sound_set_id' => $soundSetId,
        ));
        $filePaths = array(
            'image' => 'sound_set_image_upload_s3',
        );
        $soundSet = $this->getObjectsUrl($soundSet->getArrayCopy(),$filePaths,'one');
        return $this->success($soundSet);
        
    }
    
    /**
     * Update order sound set in sound board of an user
     * 
     * @param mixed $data
     * @return
     */
    public function replaceList($data)
    {
        $userId = $this->identity()->id;
        $soundSetsId = explode(',',$data['sound_set_id']);
        $orders = explode(',',$data['order']);
        if(count($soundSetsId) != count($orders)) {
            return $this->error('Please pass the right format to using this service',400);    
        }
        for($i = 0; $i < count($soundSetsId); $i++) {
            $userSoundSet = $this->getUserSoundSetDao()->fetchOne($soundSetsId[$i],$userId);
            if($userSoundSet) {
                $userSoundSet->order = $orders[$i];
                $this->getUserSoundSetDao()->update($userSoundSet);    
            }
        }
        return $this->success();
    }
    
    /**
     * Active/Inactive a soundset
     * 
     * @param mixed $id
     * @param mixed $data
     * @return void
     */
    public function update($id, $data)
    {
        $soundSetId = (int) $id;
        $userId = $this->identity()->id;
        $userSoundSet = $this->getUserSoundSetDao()->fetchOne($soundSetId,$userId);
        if(!$userSoundSet) {
            return $this->error('This sound set is not in your sound board so you can active or inactive it');
        }
        if($this->getSoundSetBusiness()->activeInactiveSoundSet($userSoundSet)) {
            return $this->success();    
        }
        
        return $this->error('Can not using this service',400);
        
    }
    
    /**
     * Get all soundsets are free and have not yet in the list soundboard of an user
     * 
     * @return void
     */
//    public function getSoundSetFreeAction()
//    {
//        $userId = $this->identity()->id;
//        $userSoundSets = $this->getUserSoundSetDao()->fetchAllBy('user_id',$userId);
//        $soundSetIdExist = array();
//        foreach($userSoundSets as $userSoundSet) {
//            $soundSetIdExist[] = $userSoundSet->sound_set_id;
//        }
//        $soundSetsFree = $this->getSoundSetDao()->fetchFreeSoundSet($soundSetIdExist);
//        echo '<pre>'; var_dump($soundSetsFree->toArray()); echo '</pre>'; die;
//    }

    public function get($id) {}
    
}

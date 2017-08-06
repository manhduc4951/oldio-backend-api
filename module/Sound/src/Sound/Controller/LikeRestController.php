<?php
namespace Sound\Controller;

use Api\Controller\AbstractMyRestfulController;
use Zend\View\Model\JsonModel;

class LikeRestController extends AbstractMyRestfulController
{  
    protected $likeDao;
    
    protected $soundDao;
    
    protected $settingsBusiness;
    
    public function getLikeDao()
    {
        if (!$this->likeDao) {
            $sm = $this->getServiceLocator();
            $this->likeDao = $sm->get('Sound\Model\LikeDao');
        }
        return $this->likeDao;
    }
    
    public function getSoundDao()
    {
        if (!$this->soundDao) {
            $sm = $this->getServiceLocator();
            $this->soundDao = $sm->get('Sound\Model\SoundDao');
        }
        return $this->soundDao;
    }
    
    public function getSettingsBusiness()
    {
        if(!$this->settingsBusiness) {
            $sm = $this->getServiceLocator();
            $this->settingsBusiness = $sm->get('Settings\Business\SettingsBussiness');
        }
        return $this->settingsBusiness;
    }

    /**
     * Like
     * 
     * @param mixed $data
     * @return
     */
    public function create($data)
    {
        $userId = $this->identity()->id;
        $data['user_id'] = $userId;
        
        $form = new \Sound\Form\LikeForm;
        $likeFormFilter = new \Sound\Form\LikeFormFilter;
        $form->setInputFilter($likeFormFilter->getInputFilter());        
        $form->setData($data);
        if ($form->isValid()) {
            $dataForm = $form->getData();
            if ($this->getLikeDao()->fetchOne($dataForm['sound_id'],$dataForm['user_id'])) {
                return $this->error('You can not like a sound twice.',400);    
            }
            if (!$this->getSoundDao()->fetchOne($dataForm['sound_id'])) {
                return $this->error('The sound you request does not exist !');     
            }
            $likeDto = new \Sound\Model\Dto\LikeDto;
            $likeDto->exchangeArray($dataForm);            
            $this->getLikeDao()->save($likeDto);
            $this->getSettingsBusiness()->push(array(
                'type' => \Settings\Model\Dto\SettingsDto::PUSH_LIKE_YOUR_SOUND,
                'id' => $likeDto->sound_id,
                'user_id' => $userId,
            ));
            
            return $this->success();                               

        } else {            
            return $this->formInvalid();
        }

    }
    
    /**
     * Unlike
     * 
     * @param mixed $id
     * @return void
     */
    public function delete($id)
    {
        $soundId = $id;
        $userId = $this->identity()->id;
        $sound = $this->getSoundDao()->fetchOne($soundId);
        if(!$sound) {
            return $this->error('The sound you request does not exist');
        }
        $like = $this->getLikeDao()->fetchOne($soundId,$userId);
        if(!$like) {
            return $this->error('You have not yet liked this sound so you can not unlike it');
        }
        $this->getLikeDao()->deleteBy('id',$like->id);
        return $this->success();
    }
    
    /**
     * Check user liked a sound or not
     * 
     * @return void
     */
    public function getList()
    {
        $userId = $this->params()->fromQuery('user_id',0);
        $soundId = $this->params()->fromQuery('sound_id',0);
        if(!$userId || !$soundId) {
            return $this->error('Please check the parameters you entered');
        }
        $like = $this->getLikeDao()->fetchOne((int)$soundId,(int)$userId);
        if($like) {
            return $this->success(array('liked' => 1));    
        } else {
            return $this->success(array('liked' => 0));
        }
    }

    public function update($id, $data) {}

    public function get($id) {}
    
}

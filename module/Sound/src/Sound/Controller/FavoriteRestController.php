<?php
namespace Sound\Controller;

use Api\Controller\AbstractMyRestfulController;
use Zend\View\Model\JsonModel;

class FavoriteRestController extends AbstractMyRestfulController
{  
    protected $favoriteDao;
    
    protected $soundDao;
    
    public function getFavoriteDao()
    {
        if (!$this->favoriteDao) {
            $sm = $this->getServiceLocator();
            $this->favoriteDao = $sm->get('Sound\Model\FavoriteDao');
        }
        return $this->favoriteDao;
    }
    
    public function getSoundDao()
    {
        if(!$this->soundDao) {
            $sm = $this->getServiceLocator();
            $this->soundDao = $sm->get('Sound\Model\SoundDao');
        }
        return $this->soundDao;
    }
    
    public function getList()
    {
        $userId = $this->identity()->id;
        $limit = $this->params()->fromQuery('limit',null);
        $offset = $this->params()->fromQuery('offset',null);
        $updated_at = $this->params()->fromQuery('updated_at',null);
        
        $criteria = array(
            'user' => $userId,
            'limit' => $limit,
            'offset' => $offset,
            'updated_at' => $updated_at,
        );
        
        $favorites = $this->getFavoriteDao()->fetchFavoriteSounds($criteria);
        $filePaths = array(
            'thumbnail' => 'sound_thumbnail_path_upload',
            'thumbnail2' => 'sound_thumbnail2_path_upload',
            'thumbnail3' => 'sound_thumbnail3_path_upload',
            'sound_path' => 'sound_file_path_upload',
        );
        $favorites = $this->getObjectsUrl($favorites,$filePaths); 
        
        return $this->success(array(
            'total_favorite' => count($favorites),
            'favorites' => $favorites
        ));
        
    }

    public function get($id) {}

    public function create($data)
    {
        $userId = $this->identity()->id;
        $soundId = $data['sound_id'];
        
        if(!$this->getSoundDao()->fetchOne($soundId)) {
            return $this->error('The sound you request does not exist');
        }
        
        if($this->getFavoriteDao()->fetchOne($soundId,$userId)) {
            return $this->error('This sound has already in your favorite list',400);
        }
        
        $favoriteDto = new \Sound\Model\Dto\FavoriteDto;
        $favoriteDto->exchangeArray($data);
        $favoriteDto->user_id = $userId;
        $favoriteDto->created_at = date('Y-m-d H:i:s');
        $this->getFavoriteDao()->save($favoriteDto);
        
        return $this->success();
    }

    public function update($id, $data) {}

    public function delete($id)
    {
        $userId = $this->identity()->id;
        $soundId = (int) $id;
        
        if(!$this->getSoundDao()->fetchOne($soundId)) {
            return $this->error('The sound you request does not exist');
        }
        
        if(!$this->getFavoriteDao()->fetchOne($soundId,$userId)) {
            return $this->error('This sound is not in your favorite list so you can not remove it');
        }
        
        $this->getFavoriteDao()->delete($soundId,$userId);
        
        return $this->success();
    }
    
    /**
     * Update favorite list of an user
     * 
     * @param mixed $data
     * @return
     */
    public function replaceList($data)
    {
        $userId = $this->identity()->id;
        $soundsId = explode(',',$data['sound_id']);
        $orders = explode(',',$data['order']);
        if(count($soundsId) != count($orders)) {
            return $this->error('Please pass the right format to using this service',400);    
        }
        for($i = 0; $i < count($soundsId); $i++) {
            $favorite = $this->getFavoriteDao()->fetchOne($soundsId[$i],$userId);
            if($favorite) {
                $favorite->order = $orders[$i];
                $this->getFavoriteDao()->update($favorite);
            }
            
        }
        return $this->success();
    }


}

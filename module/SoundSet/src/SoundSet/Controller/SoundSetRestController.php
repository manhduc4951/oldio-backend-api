<?php
namespace SoundSet\Controller;

use Api\Controller\AbstractMyRestfulController;
use Zend\View\Model\JsonModel;

class SoundSetRestController extends AbstractMyRestfulController
{  
    protected $soundSetDao;
    
    protected $soundSetItemDao;
    
    public function getSoundSetDao()
    {
        if(!$this->soundSetDao) {
            $sm = $this->getServiceLocator();
            $this->soundSetDao = $sm->get('SoundSet\Model\SoundSetDao');
        }
        return $this->soundSetDao;
    }
    
    public function getSoundSetItemDao()
    {
        if(!$this->soundSetItemDao) {
            $sm = $this->getServiceLocator();
            $this->soundSetItemDao = $sm->get('SoundSet\Model\SoundSetItemDao');
        }
        return $this->soundSetItemDao;
    }

    /**
     * Get all list soundset in shop
     * 
     * @return
     */
    public function getList()
    {  
        $limit = $this->params()->fromQuery('limit',null);
        $offset = $this->params()->fromQuery('offset',null);
        $updated = $this->params()->fromQuery('updated_at',null);
        $price = $this->params()->fromQuery('price',null);
        
        $criteria = array(
            'limit' => $limit,
            'offset' => $offset,
            'updated_at' => $updated,
            'price' => $price,
        );
        
        $soundsSet = $this->getSoundSetDao()->fetchAll($criteria);
        $filePaths = array(
            'image' => 'sound_set_image_upload_s3',
            'zip_file' => 'sound_set_zip_upload_s3',
        );
        $soundsSet = $this->getObjectsUrl($soundsSet,$filePaths);       
        foreach($soundsSet as &$soundSet) {
            $soundSet['sound_set_id'] = $soundSet['id'];
        }
        
        return $this->success($soundsSet);
    }

    /**
     * Get details one soundset 
     * 
     * @param mixed $id
     * @return
     */
    public function get($id)
    {
        $soundSetId = (int) $id;
        $soundSet = $this->getSoundSetDao()->fetchOne($soundSetId);
        if(!$soundSet) {
            return $this->error('The sound set you request does not exist');
        }
        $filePaths = array(
            'image' => 'sound_set_image_upload_s3',
            'zip_file' => 'sound_set_zip_upload_s3',
        );
        $soundSet = $this->getObjectsUrl($soundSet->getArrayCopy(),$filePaths,'one');
        $soundSetItems = $this->getSoundSetItemDao()->fetchAllBy('sound_set_id',$soundSetId);
        $filePathItems = array(
            'file' => 'sound_set_item_upload_s3',
            'image' => 'sound_set_item_upload_s3',
        );
        $soundSetItems = $this->getObjectsUrl($soundSetItems,$filePathItems,'many',$soundSetId);
        $soundSet['items'] = $soundSetItems;
        
        return $this->success($soundSet);
    }

    public function create($data) {}

    public function update($id, $data) {}

    public function delete($id) {}

}

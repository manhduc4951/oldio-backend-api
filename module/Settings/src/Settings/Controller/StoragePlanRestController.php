<?php
namespace Settings\Controller;

use Api\Controller\AbstractMyRestfulController;
use Zend\View\Model\JsonModel;

class StoragePlanRestController extends AbstractMyRestfulController
{
    protected $storagePlanDao;
    
    public function getStoragePlanDao()
    {
        if(!$this->storagePlanDao) {
            $sm = $this->getServiceLocator();
            $this->storagePlanDao = $sm->get('Settings\Model\StoragePlanDao');
        }
        return $this->storagePlanDao;
    }
    
    public function getList()
    {
        $storagePlans = $this->getStoragePlanDao()->fetchAll()->toArray();
        $s3client = $this->getServiceLocator()->get('Aws')->get('S3');
        $config = $this->getServiceLocator()->get('config');
        foreach($storagePlans as &$storagePlan) {
            if($storagePlan['image'] && $s3client->doesObjectExist($config['config_ica467']['default_bucket'],$config['config_ica467']['storage_plan_image'].'/'.$storagePlan['image'])) {
                $storagePlan['image'] = $this->getS3Url($config['config_ica467']['storage_plan_image'].'/'.$storagePlan['image']);
            } else {
                $storagePlan['image'] = null;
            }
        }
        return $this->success($storagePlans);
    }

    public function get($id) {}
    

    public function create($data) {}
    

    public function update($id, $data) {}
    

    public function delete($id) {}
    
}

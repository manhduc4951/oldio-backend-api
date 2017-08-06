<?php
namespace SoundSet\Business;

use Zend\Di\ServiceLocator;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use VIPSoft\Unzip\Unzip;

class SoundSetBusiness implements ServiceManagerAwareInterface
{	
 	protected $serviceManager;
    
    protected $userSoundSetDao;
    
    protected $soundSetDao;
    
    protected $soundSetItemDao;
 	
 	public function setServiceManager(ServiceManager $serviceManager)
 	{
 	    $this->serviceManager = $serviceManager;
 	}
 	
 	public function getServiceManager()
 	{
 	    return $this->serviceManager;
 	}
    
    public function getUserSoundSetDao()
    {
        if(!$this->userSoundSetDao) {
            
            $this->userSoundSetDao = $this->getServiceManager()->get('SoundSet\Model\UserSoundSetDao');
        }
        return $this->userSoundSetDao;
    }
    
    public function getSoundSetDao()
    {
        if(!$this->soundSetDao) {
            $this->soundSetDao = $this->getServiceManager()->get('SoundSet\Model\SoundSetDao');
        }
        return $this->soundSetDao;
    }
    
    public function getSoundSetItemDao()
    {
        if(!$this->soundSetItemDao) {
            $this->soundSetItemDao = $this->getServiceManager()->get('SoundSet\Model\SoundSetItemDao');
        }
        return $this->soundSetItemDao;
    }
    
    /**
     * Business for active/inactive a soundset in soundboard of an user
     * 
     * @return void
     */
    public function activeInactiveSoundSet(\SoundSet\Model\Dto\UserSoundSetDto $userSoundSet)
    {
        
        if($userSoundSet->type == 1) {
            $userSoundSet->type = 0;            
            $this->getUserSoundSetDao()->update($userSoundSet);
            return true;
        } elseif($userSoundSet->type == 0) {
            $userSoundSet->type = 1;
            $this->getUserSoundSetDao()->update($userSoundSet);
            return true;
        } else {
            return false;
        }
         
    }
    
    /**
     * Create soundset and upload to s3
     * 
     * @param mixed $soundSet
     * @return
     */
    public function createSoundSet(\SoundSet\Model\Dto\SoundSetDto $soundSet)
    {  
        try {
        $this->getServiceManager()->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection()->beginTransaction();
        $config = $this->getServiceManager()->get('config');
        $soundSetImage = $soundSet->image;
        if(is_array($soundSetImage) && ($soundSetImage['tmp_name'])) {
            $soundSet->image = pathinfo($soundSetImage['tmp_name'], PATHINFO_BASENAME);
        } else {
            unset($soundSet->image);
        }
        
        $soundSetFile = $soundSet->zip_file;
        if(is_array($soundSetFile) && ($soundSetFile['tmp_name'])) {
            $soundSet->zip_file = pathinfo($soundSetFile['tmp_name'], PATHINFO_BASENAME);
        } else {
            unset($soundSet->zip_file);
        }
        if(!$soundSet->price) {
            $soundSet->price = 0.00;
        }
        $soundSet->created_at = date('Y-m-d H:i:s');
        $soundSet->updated_at = date('Y-m-d H:i:s');
        $soundSetId = $this->getSoundSetDao()->save($soundSet);
        /*Unzip and save each sound set item into db*/
        mkdir($config['config_ica467']['sound_set_item_upload_local'].'/'.$soundSetId);
        $unzipper  = new Unzip();
        $filenames = $unzipper->extract($config['config_ica467']['sound_set_zip_upload_local'].'/'.$soundSet->zip_file,
                                        $config['config_ica467']['sound_set_item_upload_local'].'/'.$soundSetId
                                        );
        $images = array();
        $files = array();
        foreach($filenames as $name) {
            if(in_array(pathinfo($name,PATHINFO_EXTENSION ),$config['config_ica467']['image_type'])) {
                $images[] = $name;
            }    
            if(in_array(pathinfo($name,PATHINFO_EXTENSION),$config['config_ica467']['sound_type'])) {
                $files[] = $name;
            }        
        }
        /*Rollback if have no soundset file*/
        if(empty($files)) {
            /*delete all files have just upload*/
             if($soundSet->image) {
                unlink($config['config_ica467']['sound_set_image_upload_local'].'/'.$soundSet->image);
             }
             if($soundSet->zip_file) {
                unlink($config['config_ica467']['sound_set_zip_upload_local'].'/'.$soundSet->zip_file);
             }
             $deleteFiles = $this->getServiceManager()->get('ControllerPluginManager')->get('DeleteFiles');
             $deleteFiles->delete($config['config_ica467']['sound_set_item_upload_local'].'/'.$soundSetId,true,1);
             
             return false;
        } else {
            /*Upload local files to S3*/
            $s3client = $this->getServiceManager()->get('Aws')->get('S3');
            if($soundSet->image && file_exists($config['config_ica467']['sound_set_image_upload_local'].'/'.$soundSet->image))
            {
                $s3client->upload(
                $config['config_ica467']['default_bucket'],
                $config['config_ica467']['sound_set_image_upload_s3'].'/'.$soundSet->image,
                fopen($config['config_ica467']['sound_set_image_upload_local'].'/'.$soundSet->image,'r+')
                );
            }
            if($soundSet->zip_file && file_exists($config['config_ica467']['sound_set_zip_upload_local'].'/'.$soundSet->zip_file))
            {
                $s3client->upload(
                    $config['config_ica467']['default_bucket'],
                    $config['config_ica467']['sound_set_zip_upload_s3'].'/'.$soundSet->zip_file,
                    fopen($config['config_ica467']['sound_set_zip_upload_local'].'/'.$soundSet->zip_file,'r+')
                );    
            }
            $s3client->uploadDirectory(
                $config['config_ica467']['sound_set_item_upload_local'].'/'.$soundSetId,
                $config['config_ica467']['default_bucket'],
                $config['config_ica467']['sound_set_item_upload_s3'].'/'.$soundSetId
            );
        }
        
        for($i=0;$i<count($files);$i++) {
            $soundSetItem = new \SoundSet\Model\Dto\SoundSetItemDto;
            $soundSetItem->id = 0;
            $soundSetItem->name = pathinfo($files[$i],PATHINFO_FILENAME);
            $soundSetItem->sound_set_id = $soundSetId;
            $fileName = pathinfo($files[$i],PATHINFO_FILENAME);
            $imageKey = $this->getImageKey($images,$fileName);
            if(is_int($imageKey)) {
                $soundSetItem->image = $images[$imageKey];    
            } 
            $soundSetItem->file = $files[$i];
            $soundSetItem->created_at = date('Y-m-d H:i:s');
            $this->getSoundSetItemDao()->save($soundSetItem);
        }
        $this->getServiceManager()->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection()->commit();
        } catch(\Exception $e) {
             die($e->getMessage());
             $this->getServiceManager()->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection()->rollback();
        }                                
        
    }
    
    /**
     * Update soundset and upload to s3
     * 
     * @param mixed $soundSet
     * @return
     */
    public function updateSoundSet(\SoundSet\Model\Dto\SoundSetDto $soundSet)
    {
        try {
        $this->getServiceManager()->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection()->beginTransaction();
        $config = $this->getServiceManager()->get('config');
        $soundSetImage = $soundSet->image;
        if(is_array($soundSetImage) && ($soundSetImage['tmp_name'])) {
            $soundSet->image = pathinfo($soundSetImage['tmp_name'], PATHINFO_BASENAME);
        } else {
            unset($soundSet->image);
        }
        
        $soundSetFile = $soundSet->zip_file;
        if(is_array($soundSetFile) && ($soundSetFile['tmp_name'])) {
            $soundSet->zip_file = pathinfo($soundSetFile['tmp_name'], PATHINFO_BASENAME);
        } else {
            unset($soundSet->zip_file);
        }
        $soundSet->updated_at = date('Y-m-d H:i:s');
        $oldSoundSetImage = $soundSet->old_image;
        $oldSoundSetZipFile = $soundSet->old_zip_file;
        unset($soundSet->old_image);
        unset($soundSet->old_zip_file);
        if(!$soundSet->price) {
            $soundSet->price = 0.00;
        }
        $this->getSoundSetDao()->save($soundSet);
        $s3client = $this->getServiceManager()->get('Aws')->get('S3');
        /*Unzip and save each sound set item into db*/
        if(empty($soundSet->zip_file)) {
            /*no zip file but have to check image file, if user change image sound set, need to unlink old image*/
            if(!empty($soundSet->image) && ($oldSoundSetImage)) {
                $s3client->deleteMatchingObjects($config['config_ica467']['default_bucket'],$config['config_ica467']['sound_set_image_upload_s3'].'/'.$oldSoundSetImage);
                $s3client->upload(
                    $config['config_ica467']['default_bucket'],
                    $config['config_ica467']['sound_set_image_upload_s3'].'/'.$soundSet->image,
                    fopen($config['config_ica467']['sound_set_image_upload_local'].'/'.$soundSet->image,'r+')
                );
            }
            $this->getServiceManager()->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection()->commit();
            return true;    
        }
        $unzipper  = new Unzip();
        $filenames = $unzipper->extract($config['config_ica467']['sound_set_zip_upload_local'].'/'.$soundSet->zip_file,
                                        $config['config_ica467']['sound_set_item_tmp_upload_local']
                                        );
        $images = array();
        $files = array();
        foreach($filenames as $name) {
            if(in_array(pathinfo($name,PATHINFO_EXTENSION ),$config['config_ica467']['image_type'])) {
                $images[] = $name;
            } 
            if(in_array(pathinfo($name,PATHINFO_EXTENSION),$config['config_ica467']['sound_type'])) {
                $files[] = $name;
            }        
        }
        /*Rollback if have no soundset file*/
        $deleteFiles = $this->getServiceManager()->get('ControllerPluginManager')->get('DeleteFiles');
        if(empty($files)) {
            /*delete all files have just upload*/
             if($soundSet->image) {
                unlink($config['config_ica467']['sound_set_image_upload_local'].'/'.$soundSet->image);
             }
             if($soundSet->zip_file) {
                unlink($config['config_ica467']['sound_set_zip_upload_local'].'/'.$soundSet->zip_file);
             }
             
             return false;
        } else {
            if($oldSoundSetImage && !empty($soundSet->image)) {
                $s3client->deleteMatchingObjects($config['config_ica467']['default_bucket'],$config['config_ica467']['sound_set_image_upload_s3'].'/'.$oldSoundSetImage);
                $s3client->upload(
                    $config['config_ica467']['default_bucket'],
                    $config['config_ica467']['sound_set_image_upload_s3'].'/'.$soundSet->image,
                    fopen($config['config_ica467']['sound_set_image_upload_local'].'/'.$soundSet->image,'r+')
                );    
            }
            if($oldSoundSetZipFile && !empty($soundSet->zip_file)) {
                $s3client->deleteMatchingObjects($config['config_ica467']['default_bucket'],$config['config_ica467']['sound_set_zip_upload_s3'].'/'.$oldSoundSetZipFile);
                $s3client->upload(
                    $config['config_ica467']['default_bucket'],
                    $config['config_ica467']['sound_set_zip_upload_s3'].'/'.$soundSet->zip_file,
                    fopen($config['config_ica467']['sound_set_zip_upload_local'].'/'.$soundSet->zip_file,'r+')
                );     
            }
            $deleteFiles->delete($config['config_ica467']['sound_set_item_upload_local'].'/'.$soundSet->id);
            $unzipper->extract(
                $config['config_ica467']['sound_set_zip_upload_local'].'/'.$soundSet->zip_file,
                $config['config_ica467']['sound_set_item_upload_local'].'/'.$soundSet->id
            );
            $s3client->deleteMatchingObjects($config['config_ica467']['default_bucket'],$config['config_ica467']['sound_set_item_upload_s3'].'/'.$soundSet->id);
            $s3client->uploadDirectory(
                $config['config_ica467']['sound_set_item_upload_local'].'/'.$soundSet->id,
                $config['config_ica467']['default_bucket'],
                $config['config_ica467']['sound_set_item_upload_s3'].'/'.$soundSet->id
            );
            /*update database*/
            /*delete old sound set item in db*/
            $this->getSoundSetItemDao()->deleteBy('sound_set_id',$soundSet->id);
            /*update new sound set item in db*/
            for($i=0;$i<count($files);$i++) {
                $soundSetItem = new \SoundSet\Model\Dto\SoundSetItemDto;
                $soundSetItem->id = 0;
                $soundSetItem->name = pathinfo($files[$i],PATHINFO_FILENAME);
                $soundSetItem->sound_set_id = $soundSet->id;
                $fileName = pathinfo($files[$i],PATHINFO_FILENAME);
                $imageKey = $this->getImageKey($images,$fileName);
                if(is_int($imageKey)) {
                    $soundSetItem->image = $images[$imageKey];    
                } 
                $soundSetItem->file = $files[$i];
                $soundSetItem->created_at = date('Y-m-d H:i:s');
                $this->getSoundSetItemDao()->save($soundSetItem);
            }
        }
        $deleteFiles->delete($config['config_ica467']['sound_set_item_tmp_upload_local'],true,1);
        mkdir($config['config_ica467']['sound_set_item_upload_local'].'/tmp');
        $this->getServiceManager()->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection()->commit();
        } catch(\Exception $e) {
             die($e->getMessage());
             $this->getServiceManager()->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection()->rollback();
        } 
    }

    
    /**
     * Delete soundset on S3
     * 
     * @param mixed $soundSet
     * @return void
     */
    public function deleteSoundSet(\SoundSet\Model\Dto\SoundSetDto $soundSet)
    {
        $config = $this->getServiceManager()->get('config');
        $soundSetId = $soundSet->id;
        $s3client = $this->getServiceManager()->get('Aws')->get('S3');
        if($soundSet->image && $s3client->doesObjectExist($config['config_ica467']['default_bucket'],$config['config_ica467']['sound_set_image_upload_s3'].'/'.$soundSet->image)) {

            $s3client->deleteMatchingObjects($config['config_ica467']['default_bucket'],$config['config_ica467']['sound_set_image_upload_s3'].'/'.$soundSet->image);
        }
        if($soundSet->zip_file && $s3client->doesObjectExist($config['config_ica467']['default_bucket'],$config['config_ica467']['sound_set_zip_upload_s3'].'/'.$soundSet->zip_file)) {

            $s3client->deleteMatchingObjects($config['config_ica467']['default_bucket'],$config['config_ica467']['sound_set_zip_upload_s3'].'/'.$soundSet->zip_file);
        }
        $s3client->deleteMatchingObjects($config['config_ica467']['default_bucket'],$config['config_ica467']['sound_set_item_upload_s3'].'/'.$soundSet->id);
        $this->getSoundSetDao()->delete($soundSetId);

        
    }
    
    /**
     * Get sound set item image (key in array images) respective with sound set file
     * 
     * @param mixed $imageArray
     * @param mixed $fileName
     * @return
     */
    public function getImageKey($imageArray,$fileName)
    {  
        $config = $this->getServiceManager()->get('config');
        $imagesExtension = $config['config_ica467']['image_type'];
        foreach($imagesExtension as $imageExtension) {
            $key = array_search($fileName.'.'.$imageExtension,$imageArray);
            if(is_int($key)) break;
        }
        return $key;
    }
    
}
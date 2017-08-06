<?php
namespace Sound\Business;

use Zend\Di\ServiceLocator;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class SoundBusiness implements ServiceManagerAwareInterface
{	
 	protected $serviceManager;
    
    protected $soundDao;
    
    protected $commentDao;
 	
    protected $likeDao;
    
    protected $viewDao;
    
    protected $favoriteDao;
    
    protected $soundCategoryDao;
    
 	public function setServiceManager(ServiceManager $serviceManager)
 	{
 	    $this->serviceManager = $serviceManager;
 	}
 	
 	public function getServiceManager()
 	{
 	    return $this->serviceManager;
 	}
    
    public function getSoundDao()
    {
        if (!$this->soundDao) {
            $this->soundDao = $this->getServiceManager()->get('Sound\Model\SoundDao');
        }
        return $this->soundDao;
    }
    
    public function getCommentDao()
    {
        if(!$this->commentDao) {
            $this->commentDao = $this->getServiceManager()->get('Sound\Model\CommentDao');
        }
        return $this->commentDao;
    }
    
    public function getFavoriteDao()
    {
        if(!$this->favoriteDao) {
            $this->favoriteDao = $this->getServiceManager()->get('Sound\Model\FavoriteDao');
        }
        return $this->favoriteDao;
    }
    
    public function getLikeDao()
    {
        if(!$this->likeDao) {
            $this->likeDao = $this->getServiceManager()->get('Sound\Model\LikeDao');
        }
        return $this->likeDao;
    }
    
    public function getViewDao()
    {
        if(!$this->viewDao) {
            $this->viewDao = $this->getServiceManager()->get('Sound\Model\ViewDao');
        }
        return $this->viewDao;
    }
    
    public function getSoundCategoryDao()
    {
        if(!$this->soundCategoryDao) {
            $this->soundCategoryDao = $this->getServiceManager()->get('User\Model\SoundCategoryDao');
        }
        return $this->soundCategoryDao;
    }
    
    /**
     * Business function to update/edit sound (s3)
     * 
     * @param mixed $sound
     * @param mixed $data
     * @param mixed $oldThumbnail
     * @param mixed $oldSoundPath
     * @return void
     */
    public function updateSoundRest(\Sound\Model\Dto\SoundDto $sound)
    {  
        $config = $this->getServiceManager()->get('config');
        $s3client = $this->getServiceManager()->get('Aws')->get('S3');
        $thumbnail = $sound->thumbnail;
        if(is_array($thumbnail) && ($thumbnail['tmp_name'])) {
            $sound->thumbnail = pathinfo($thumbnail['tmp_name'], PATHINFO_BASENAME);
            if($sound->old_thumbnail && $s3client->doesObjectExist($config['config_ica467']['default_bucket'],$config['config_ica467']['sound_thumbnail_path_upload'].'/'.$sound->old_thumbnail)) {
                $s3client->deleteMatchingObjects($config['config_ica467']['default_bucket'],$config['config_ica467']['sound_thumbnail_path_upload'].'/'.$sound->old_thumbnail);    
            }
        } else {
            unset($sound->thumbnail);
        }
        //
        $thumbnail2 = $sound->thumbnail2;
        if(is_array($thumbnail2) && ($thumbnail2['tmp_name'])) {
            $sound->thumbnail2 = pathinfo($thumbnail2['tmp_name'], PATHINFO_BASENAME);
            if($sound->old_thumbnail2 && $s3client->doesObjectExist($config['config_ica467']['default_bucket'],$config['config_ica467']['sound_thumbnail2_path_upload'].'/'.$sound->old_thumbnail2)) {
                $s3client->deleteMatchingObjects($config['config_ica467']['default_bucket'],$config['config_ica467']['sound_thumbnail2_path_upload'].'/'.$sound->old_thumbnail2);    
            }
        } else {
            unset($sound->thumbnail2);
        }
        //
        $thumbnail3 = $sound->thumbnail3;
        if(is_array($thumbnail3) && ($thumbnail3['tmp_name'])) {
            $sound->thumbnail3 = pathinfo($thumbnail3['tmp_name'], PATHINFO_BASENAME);
            if($sound->old_thumbnail3 && $s3client->doesObjectExist($config['config_ica467']['default_bucket'],$config['config_ica467']['sound_thumbnail3_path_upload'].'/'.$sound->old_thumbnail3)) {
                $s3client->deleteMatchingObjects($config['config_ica467']['default_bucket'],$config['config_ica467']['sound_thumbnail3_path_upload'].'/'.$sound->old_thumbnail3);    
            }
        } else {
            unset($sound->thumbnail3);
        }
        //
        
        $soundPath = $sound->sound_path;
        if(is_array($soundPath) && ($soundPath['tmp_name'])) {
            $sound->sound_path = pathinfo($soundPath['tmp_name'], PATHINFO_BASENAME);
            if($sound->old_sound_path && $s3client->doesObjectExist($config['config_ica467']['default_bucket'],$config['config_ica467']['sound_file_path_upload'].'/'.$sound->old_sound_path)) {
                $s3client->deleteMatchingObjects($config['config_ica467']['default_bucket'],$config['config_ica467']['sound_file_path_upload'].'/'.$sound->old_sound_path);    
            }
        } else {
            unset($sound->sound_path);
        }
        
        $sound->updated_at = date('Y-m-d H:i:s');
        unset($sound->old_thumbnail);
        unset($sound->old_thumbnail2);
        unset($sound->old_thumbnail3);
        unset($sound->old_sound_path);
        $categoryId = $sound->category_id;
        unset($sound->category_id);
        $this->getSoundDao()->save($sound);
        /*update to sound_category table*/
        if(!empty($categoryId)) {
            $this->getSoundCategoryDao()->deleteBy('sound_id',$sound->id);
            if(is_array($categoryId)) {
                $categories = $categoryId;    
            } else {
                $categories = explode(',',$categoryId);    
            }
            foreach($categories as $category) {
                $soundCategory = new \User\Model\Dto\SoundCategoryDto;
                $soundCategory->id = 0;
                $soundCategory->sound_id = $sound->id;
                $soundCategory->category_id = (int)$category;
                $this->getSoundCategoryDao()->save($soundCategory);
            }    
        } 
    }
    
    /**
     * Business function to create sound
     * 
     * @param mixed $sound
     * @return void
     */
    public function createSoundRest(\Sound\Model\Dto\SoundDto $sound)
    {  
        $soundPath = $sound->sound_path;
        if(is_array($soundPath) && ($soundPath['tmp_name'])) {
            $sound->sound_path = pathinfo($soundPath['tmp_name'], PATHINFO_BASENAME);
        } else {
            unset($sound->sound_path);
        }
        
        $thumbnail = $sound->thumbnail;
        if(is_array($thumbnail) && ($thumbnail['tmp_name'])) {
            $sound->thumbnail = pathinfo($thumbnail['tmp_name'], PATHINFO_BASENAME);
        } else {
            unset($sound->thumbnail);
        }
        
        $thumbnail2 = $sound->thumbnail2;
        if(is_array($thumbnail2) && ($thumbnail2['tmp_name'])) {
            $sound->thumbnail2 = pathinfo($thumbnail2['tmp_name'], PATHINFO_BASENAME);
        } else {
            unset($sound->thumbnail2);
        }
        
        $thumbnail3 = $sound->thumbnail3;
        if(is_array($thumbnail3) && ($thumbnail3['tmp_name'])) {
            $sound->thumbnail3 = pathinfo($thumbnail3['tmp_name'], PATHINFO_BASENAME);
        } else {
            unset($sound->thumbnail3);
        }
        
        $sound->created_at = date('Y-m-d H:i:s');
        $sound->updated_at = date('Y-m-d H:i:s');
        $categoryId = $sound->category_id;
        unset($sound->category_id);
        $soundId = $this->getSoundDao()->save($sound);
        /*update to sound_category table*/
        if(!empty($categoryId)) {
            if(is_array($categoryId)) {
                $categories = $categoryId;    
            } else {
                $categories = explode(',',$categoryId);    
            }
            foreach($categories as $category) {
                $soundCategory = new \User\Model\Dto\SoundCategoryDto;
                $soundCategory->id = 0;
                $soundCategory->sound_id = $soundId;
                $soundCategory->category_id = (int)$category;
                $this->getSoundCategoryDao()->save($soundCategory);
            }    
        }
        return $soundId;           
    }
    
     /**
     * Delete sound and relative to itself (s3)
     * 
     * @param mixed $sound
     * @return void
     */
    public function deleteSoundRest(\Sound\Model\Dto\SoundDto $sound)
    {
        try {
            $this->getServiceManager()->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection()->beginTransaction();
            $config = $this->getServiceManager()->get('config');
            $s3client = $this->getServiceManager()->get('Aws')->get('S3');
            if($sound->thumbnail && $s3client->doesObjectExist($config['config_ica467']['default_bucket'],$config['config_ica467']['sound_thumbnail_path_upload'].'/'.$sound->thumbnail)) {
                $s3client->deleteMatchingObjects($config['config_ica467']['default_bucket'],$config['config_ica467']['sound_thumbnail_path_upload'].'/'.$sound->thumbnail);    
            }
            if($sound->thumbnail2 && $s3client->doesObjectExist($config['config_ica467']['default_bucket'],$config['config_ica467']['sound_thumbnail2_path_upload'].'/'.$sound->thumbnail2)) {
                $s3client->deleteMatchingObjects($config['config_ica467']['default_bucket'],$config['config_ica467']['sound_thumbnail2_path_upload'].'/'.$sound->thumbnail2);    
            }
            if($sound->thumbnail3 && $s3client->doesObjectExist($config['config_ica467']['default_bucket'],$config['config_ica467']['sound_thumbnail3_path_upload'].'/'.$sound->thumbnail3)) {
                $s3client->deleteMatchingObjects($config['config_ica467']['default_bucket'],$config['config_ica467']['sound_thumbnail3_path_upload'].'/'.$sound->thumbnail3);    
            }
            if($sound->sound_path && $s3client->doesObjectExist($config['config_ica467']['default_bucket'],$config['config_ica467']['sound_file_path_upload'].'/'.$sound->sound_path)) {
                $s3client->deleteMatchingObjects($config['config_ica467']['default_bucket'],$config['config_ica467']['sound_file_path_upload'].'/'.$sound->sound_path);    
            }
            $this->getSoundDao()->delete($sound->id);
            $this->getCommentDao()->deleteBy('sound_id',$sound->id);
            $this->getLikeDao()->deleteBy('sound_id',$sound->id);
            $this->getViewDao()->deleteBy('sound_id',$sound->id);
            $this->getFavoriteDao()->deleteBy('sound_id',$sound->id);
            $this->getSoundCategoryDao()->deleteBy('sound_id',$sound->id);
            $this->getServiceManager()->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection()->commit();
        } catch (\Exception $e) {
            die($e->getMessage());
            $this->getServiceManager()->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection()->rollback();
        }
        
    }
    
    /**
     * Get all tags to search all action
     * 
     * @param mixed $tag
     * @return void
     */
    public function getTags($tag)
    {  
        $findMe = $tag;
        $tags = $this->getSoundDao()->fetchTags($tag)->toArray();
        $string = '';
        foreach($tags as $tag) {
            $string .= $tag['tags'].' ';
        }        
        $tagsArray = explode(' ',$string);
        foreach($tagsArray as &$tagString) {
            $pos = strpos(strtolower($tagString), strtolower($findMe));
            if($pos === false) {
                $tagString = '';
            }    
        }
        $tags = array_filter(array_unique($tagsArray));
        
        return array_values($tags);
    }
    
}
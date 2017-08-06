<?php
namespace User\Business;

use Zend\Di\ServiceLocator;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class UserBusiness implements ServiceManagerAwareInterface
{	
 	protected $serviceManager;
    
    protected $userDao;
    
    protected $settingsDao;
    
    protected $soundDao;
    
    protected $userSoundDao;
 	
 	public function setServiceManager(ServiceManager $serviceManager)
 	{
 	    $this->serviceManager = $serviceManager;
 	}
 	
 	public function getServiceManager()
 	{
 	    return $this->serviceManager;
 	}
    
    public function getUserDao()
    {
        if(!$this->userDao) {
            $this->userDao = $this->getServiceManager()->get('User\Model\UserDao');
        }
        return $this->userDao;
    }
    
    public function getSettingsDao()
    {
        if(!$this->settingsDao) {
            $this->settingsDao = $this->getServiceManager()->get('Settings\Model\SettingsDao');
        }
        return $this->settingsDao;
    }
    
    public function getSoundDao()
    {
        if(!$this->soundDao) {
            $this->soundDao = $this->getServiceManager()->get('Sound\Model\SoundDao');
        }
        return $this->soundDao;
    }
    
    public function getUserSoundDao()
    {
        if(!$this->userSoundDao) {
            $this->userSoundDao = $this->getServiceManager()->get('SoundSet\Model\UserSoundSetDao');
        }
        return $this->userSoundDao;
    }
    
    /**
     * Signup service business
     * 
     * @param mixed $user
     * @return
     */
    public function createUserRest(\User\Model\Dto\UserDto $user)
    {
        $user->password = md5($user->password);
        $user->created_at = date('Y-m-d H:i:s');
        $user->updated_at = date('Y-m-d H:i:s');
        $userId = $this->getUserDao()->save($user);
        
        $settings = new \Settings\Model\Dto\SettingsDto;
        $settings->id = 0;
        $settings->user_id = $userId;
        $settings->email = $user->username;
        $this->getSettingsDao()->save($settings);
        
        $userSound = new \SoundSet\Model\Dto\UserSoundSetDto;
        $userSound->sound_set_id = 1;
        $userSound->user_id = $userId;
        $userSound->created_at = date('Y-m-d H:i:s');
        $this->getUserSoundDao()->save($userSound);
        
        return $this->getUserDao()->fetchOne($userId);
    }
    
    /**
     * Upload avatar service business (s3)
     * 
     * @param mixed $user
     * @return void
     */
    public function uploadAvatarRest(\User\Model\Dto\UserDto $user)
    {
        $config = $this->getServiceManager()->get('config');
        $avatar = $user->avatar;
        $s3client = $this->getServiceManager()->get('Aws')->get('S3');
        if(is_array($avatar) && ($avatar['tmp_name'])) {
            $user->avatar = pathinfo($avatar['tmp_name'], PATHINFO_BASENAME);
            if($user->old_avatar && $s3client->doesObjectExist($config['config_ica467']['default_bucket'],$config['config_ica467']['user_avatar_path_upload'].'/'.$user->old_avatar)) {
                $s3client->deleteMatchingObjects($config['config_ica467']['default_bucket'],$config['config_ica467']['user_avatar_path_upload'].'/'.$user->old_avatar);    
            }
        } else {
            unset($user->avatar);
        }
        $user->updated_at = date('Y-m-d H:i:s');
        unset($user->old_avatar);
        $this->getUserDao()->save($user);
        return $user->avatar;
    }
    
    /**
     * Upload cover image service business (s3)
     * 
     * @param mixed $user
     * @return void
     */
    public function uploadCoverImageRest(\User\Model\Dto\UserDto $user)
    {
        $config = $this->getServiceManager()->get('config');
        $coverImage = $user->cover_image;
        $s3client = $this->getServiceManager()->get('Aws')->get('S3');
        if(is_array($coverImage) && ($coverImage['tmp_name'])) {
            $user->cover_image = pathinfo($coverImage['tmp_name'], PATHINFO_BASENAME);
            if($user->old_cover_image && $s3client->doesObjectExist($config['config_ica467']['default_bucket'],$config['config_ica467']['user_cover_path_upload'].'/'.$user->old_cover_image)) {
                $s3client->deleteMatchingObjects($config['config_ica467']['default_bucket'],$config['config_ica467']['user_cover_path_upload'].'/'.$user->old_cover_image);    
            }
        } else {
            unset($user->cover_image);
        }
        $user->updated_at = date('Y-m-d H:i:s');
        unset($user->old_cover_image);
        $this->getUserDao()->save($user);
        return $user->cover_image;
    }
    
    /**
     * Business for create user from backend
     * 
     * @param mixed $user
     * @return void
     */
    public function createUser(\User\Model\Dto\UserDto $user)
    {
        $avatar = $user->avatar;
        $coverImage = $user->cover_image;
        if(is_array($avatar) && ($avatar['tmp_name'])) {
            $user->avatar = pathinfo($avatar['tmp_name'], PATHINFO_BASENAME);
        } else {
            unset($user->avatar);
        }
        if(is_array($coverImage) && ($coverImage['tmp_name'])) {
            $user->cover_image = pathinfo($coverImage['tmp_name'], PATHINFO_BASENAME);
        } else {
            unset($user->cover_image);
        }
        $user->password = md5($user->password);
        $user->created_at = date('Y-m-d H:i:s');
        $user->updated_at = date('Y-m-d H:i:s');
        $userId = $this->getUserDao()->save($user);
        
        $settings = new \Settings\Model\Dto\SettingsDto;
        $settings->user_id = $userId;
        $settings->email = $user->username;
        $this->getSettingsDao()->save($settings);
    }
    
    /**
     * Business for update user from backend (s3)
     * 
     * @param mixed $user
     * @return void
     */
    public function updateUser(\User\Model\Dto\UserDto $user)
    {
        if($user->password) {
            $user->password = md5($user->password);
        } else {
            unset($user->password);
        }
        $config = $this->getServiceManager()->get('config');
        $s3client = $this->getServiceManager()->get('Aws')->get('S3');
        $avatar = $user->avatar;
        if(is_array($avatar) && ($avatar['tmp_name'])) {
            $user->avatar = pathinfo($avatar['tmp_name'], PATHINFO_BASENAME);
            if($user->old_avatar && $s3client->doesObjectExist($config['config_ica467']['default_bucket'],$config['config_ica467']['user_avatar_path_upload'].'/'.$user->old_avatar)) {
                $s3client->deleteMatchingObjects($config['config_ica467']['default_bucket'],$config['config_ica467']['user_avatar_path_upload'].'/'.$user->old_avatar);    
            }
        } else {
            unset($user->avatar);
        }
        
        $coverImage = $user->cover_image;
        if(is_array($coverImage) && ($coverImage['tmp_name'])) {
            $user->cover_image = pathinfo($coverImage['tmp_name'], PATHINFO_BASENAME);
            if($user->old_cover_image && $s3client->doesObjectExist($config['config_ica467']['default_bucket'],$config['config_ica467']['user_cover_path_upload'].'/'.$user->old_cover_image)) {
                $s3client->deleteMatchingObjects($config['config_ica467']['default_bucket'],$config['config_ica467']['user_cover_path_upload'].'/'.$user->old_cover_image);    
            }
        } else {
            unset($user->cover_image);
        }
        
        $user->updated_at = date('Y-m-d H:i:s');
        unset($user->old_avatar);
        unset($user->old_cover_image);
        
        $this->getUserDao()->save($user);
    }
    
    /**
     * Delete user from backend (s3)
     * 
     * @param mixed $user
     * @return void
     */
    public function deleteUser(\User\Model\Dto\UserDto $user)
    {  
        try {
            $this->getServiceManager()->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection()->beginTransaction();
            $config = $this->getServiceManager()->get('config');
            $s3client = $this->getServiceManager()->get('Aws')->get('S3');
            $sounds = $this->getSoundDao()->fetchAllBy('user_id',$user->id);
            foreach($sounds as $sound) {
                if($sound->thumbnail && $s3client->doesObjectExist($config['config_ica467']['default_bucket'],$config['config_ica467']['sound_thumbnail_path_upload'].'/'.$sound->thumbnail)) {
                    $s3client->deleteMatchingObjects($config['config_ica467']['default_bucket'],$config['config_ica467']['sound_thumbnail_path_upload'].'/'.$sound->thumbnail);    
                }
                if($sound->sound_path && $s3client->doesObjectExist($config['config_ica467']['default_bucket'],$config['config_ica467']['sound_file_path_upload'].'/'.$sound->sound_path)) {
                    $s3client->deleteMatchingObjects($config['config_ica467']['default_bucket'],$config['config_ica467']['sound_file_path_upload'].'/'.$sound->sound_path);    
                }
                $this->getSoundDao()->delete($sound->id);
            }
            if($user->avatar && $s3client->doesObjectExist($config['config_ica467']['default_bucket'],$config['config_ica467']['user_avatar_path_upload'].'/'.$user->avatar)) {
                $s3client->deleteMatchingObjects($config['config_ica467']['default_bucket'],$config['config_ica467']['user_avatar_path_upload'].'/'.$user->avatar);    
            }
            if($user->cover_image && $s3client->doesObjectExist($config['config_ica467']['default_bucket'],$config['config_ica467']['user_cover_path_upload'].'/'.$user->cover_image)) {
                $s3client->deleteMatchingObjects($config['config_ica467']['default_bucket'],$config['config_ica467']['user_cover_path_upload'].'/'.$user->cover_image);    
            }
            $this->getUserDao()->delete($user->id);
            $this->getServiceManager()->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection()->commit();
        } catch(\Exception $e) {
             die($e->getMessage());
             $this->getServiceManager()->get('Zend\Db\Adapter\Adapter')->getDriver()->getConnection()->rollback();
        }
        
    }
    
}
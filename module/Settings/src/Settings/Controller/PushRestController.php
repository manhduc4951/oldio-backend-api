<?php
namespace Settings\Controller;

use Api\Controller\AbstractMyRestfulController;
use Zend\View\Model\JsonModel;

class PushRestController extends AbstractMyRestfulController
{
    protected $commentDao;
    
    protected $soundDao;
    
    protected $settingsDao;
    
    protected $userDao;
    
    protected $deviceTokenDao;
    
    protected $notificationDao;
    
    protected $settingsBusiness;
    
    public function getCommentDao()
    {
        if (!$this->commentDao) {
            $sm = $this->getServiceLocator();
            $this->commentDao = $sm->get('Sound\Model\CommentDao');
        }
        return $this->commentDao;
    }
    
    public function getSoundDao()
    {
        if (!$this->soundDao) {
            $sm = $this->getServiceLocator();
            $this->soundDao = $sm->get('Sound\Model\SoundDao');
        }
        return $this->soundDao;
    }
    
    public function getSettingsDao()
    {
        if(!$this->settingsDao) {
            $sm = $this->getServiceLocator();
            $this->settingsDao = $sm->get('Settings\Model\SettingsDao');
        }
        return $this->settingsDao;
    }
    
    public function getUserDao()
    {
        if(!$this->userDao) {
            $sm = $this->getServiceLocator();
            $this->userDao = $sm->get('User\Model\UserDao');
        }
        return $this->userDao;
    }
    
    public function getDeviceTokenDao()
    {
        if(!$this->deviceTokenDao) {
            $sm = $this->getServiceLocator();
            $this->deviceTokenDao = $sm->get('Settings\Model\DeviceTokenDao');
        }
        return $this->deviceTokenDao;
    }
    
    public function getNotificationDao()
    {
        if(!$this->notificationDao) {
            $sm = $this->getServiceLocator();
            $this->notificationDao = $sm->get('Settings\Model\NotificationDao');
        }
        return $this->notificationDao;
    }
    
    public function getSettingsBusiness()
    {
        if(!$this->settingsBusiness) {
            $sm = $this->getServiceLocator();
            $this->settingsBusiness = $sm->get('Settings\Business\SettingsBussiness');
        }
        return $this->settingsBusiness;
    }

    public function getList() {}
    

    public function get($id) {}
    

    public function create($data)
    {
        $type = (isset($data['type'])) ? $data['type'] : null;
        if($type == \Settings\Model\Dto\SettingsDto::PUSH_COMMENT_YOUR_POST) {
            $this->pushComment($data);
        } elseif($type == \Settings\Model\Dto\SettingsDto::PUSH_LIKE_YOUR_SOUND) {
            $this->pushLike($data);
        } elseif($type == \Settings\Model\Dto\SettingsDto::PUSH_FOLLOW) {
            $this->pushFollow($data);
        }
        die;
    }
    
    public function pushComment($data)
    {
        $soundId = $data['id'];
        $userId = $data['user_id'];
        /*Send email and push notification when someone comment on my post*/
        $sound = $this->getSoundDao()->fetchOne($soundId);
        $userComment = $this->getUserDao()->fetchOne($userId);
        $userOwnSoundId = $sound->user_id;
        $userOwnSound = $this->getUserDao()->fetchOne($userOwnSoundId);
        $settings = $this->getSettingsDao()->fetchOneBy('user_id',$userOwnSoundId);
        /*Send email*/
        if($settings && $settings->email_comments_on_my_post == 2 && $userId != $userOwnSoundId) {
            try {
                $this->getSettingsBusiness()->sendEmail(array(
                    'email' => $settings->email,
                    'email_subject' => $userComment->display_name.' left a comment on your Program '.$sound->title,
                    'email_body' => $userComment->display_name.' left a comment on your Program '.$sound->title,
                ));
            } catch(Exception $e) {}
                
        }
        /*Push notification*/
        if($settings && $settings->push_follow_me && $userId != $userOwnSoundId) {
            /*Save to DB*/
            $notification = new \Settings\Model\Dto\NotificationDto;
            $notification->my_user_id = $sound->user_id;
            $notification->user_id = $userId;
            $notification->sound_id = $sound->id;
            $notification->content = '<b>'.$userComment->display_name.'</b>'.' left a comment on your Program '.'<b>'.$sound->title.'</b>';
            $notification->type = \Settings\Model\Dto\SettingsDto::PUSH_COMMENT_YOUR_POST;
            $notification->created_at = date('Y-m-d H:i:s');
            $this->getNotificationDao()->save($notification);
            /*Push to APNS*/
            $deviceTokens = $this->getDeviceTokenDao()->fetchAllBy('user_id',$userOwnSoundId);
            if(count($deviceTokens)) {
                foreach($deviceTokens as $deviceToken) {
                    $this->getSettingsBusiness()->pushNotification(array(
                        'device_token' => $deviceToken->device_token,
                        'alert' => $userComment->display_name.' left a comment on your Program '.$sound->title,
                        'badge' => count($this->getNotificationDao()->fetchAllUnreadNotification($userOwnSoundId)),
                        'custom' => array(
                            'type' => \Settings\Model\Dto\SettingsDto::PUSH_COMMENT_YOUR_POST,
                            'id' => $sound->id,
                        ),
                    ));
                }    
            }    
        }
        /*Replied in the content you have commented*/
        $comments = $this->getCommentDao()->fetchAllBy('sound_id',$soundId);
        $userIds = array();
        foreach($comments as $comment) {
            $userIds[] = $comment->user_id;
        }
        /*Get all user commented o this post except: user comment and user own sound*/
        $userIds = array_unique($userIds);
        $userIdKey = array_search($userId,$userIds);
        unset($userIds[$userIdKey]);
        $userOwnSoundKey = array_search($userOwnSoundId,$userIds);
        if($userOwnSoundKey) {
            unset($userIds[$userOwnSoundKey]);
        }        
        foreach($userIds as $userId) {
            $settings = $this->getSettingsDao()->fetchOneBy('user_id',$userId);
            if($settings && $settings->email_comments_on_a_post_i_care == 2) {
                try {
                    $this->getSettingsBusiness()->sendEmail(array(
                        'email' => $settings->email,
                        'email_subject' => $userComment->display_name.' also commented on '.$userOwnSound->display_name."'s program",
                        'email_body' => $userComment->display_name.' also commented on '.$userOwnSound->display_name."'s program",
                    ));
                } catch(Exception $e) {}    
            }
            if($settings && $settings->push_comments_on_a_post_i_care) {
                /*Save to DB*/
                $notification = new \Settings\Model\Dto\NotificationDto;
                //$notification->my_user_id = $sound->user_id;
                //$notification->user_id = $userId;
                $notification->my_user_id = $userId;
                $notification->user_id = $userComment->id;
                $notification->sound_id = $sound->id;
                $notification->content = '<b>'.$userComment->display_name.'</b>'.' also commented on '.$userOwnSound->display_name."'s program";
                $notification->type = \Settings\Model\Dto\SettingsDto::PUSH_COMMENT_YOUR_SOUND_YOU_COMMENT;
                $notification->created_at = date('Y-m-d H:i:s');
                $this->getNotificationDao()->save($notification);
                /*Push to APNS*/
                $deviceTokens = $this->getDeviceTokenDao()->fetchAllBy('user_id',$userId);
                if(count($deviceTokens)) {
                    foreach($deviceTokens as $deviceToken) {
                        $this->getSettingsBusiness()->pushNotification(array(
                            'device_token' => $deviceToken->device_token,
                            'alert' => $userComment->display_name.' also commented on '.$userOwnSound->display_name."'s program",
                            'badge' => count($this->getNotificationDao()->fetchAllUnreadNotification($userId)),
                            'custom' => array(
                                'type' => \Settings\Model\Dto\SettingsDto::PUSH_COMMENT_YOUR_SOUND_YOU_COMMENT,
                                'id' => $sound->id,
                            ),
                        ));
                    }    
                }    
            }
        }
        
        return $this->success();
    }
    
    public function pushLike($data)
    {
        $soundId = $data['id'];
        $userId = $data['user_id'];
        /*Send email and push notification when someone like my post*/
        $sound = $this->getSoundDao()->fetchOne($soundId);
        /*Dont send mail or push if the user like itself*/
        if($sound->user_id == $userId) {
            die;
        }
        $userLike = $this->getUserDao()->fetchOne($userId);
        $userOwnSound = $sound->user_id;
        $settings = $this->getSettingsDao()->fetchOneBy('user_id',$userOwnSound);
        /*Send email*/
        if($settings && $settings->email_like_my_sound == 2) {
            try {
                $this->getSettingsBusiness()->sendEmail(array(
                    'email' => $settings->email,
                    'email_subject' => $userLike->display_name.' liked your Program '.$sound->title,
                    'email_body' => $userLike->display_name.' liked your Program '.$sound->title,
                ));
            } catch(Exception $e) {}
               
        }
        /*Push notification*/
        if($settings && $settings->push_like_my_sound) {
            /*Save to DB*/
            $notification = new \Settings\Model\Dto\NotificationDto;
            $notification->my_user_id = $sound->user_id;
            $notification->user_id = $userId;
            $notification->sound_id = $sound->id;
            $notification->content = '<b>'.$userLike->display_name.'</b>'.' liked your Program '.'<b>'.$sound->title.'</b>';
            $notification->type = \Settings\Model\Dto\SettingsDto::PUSH_LIKE_YOUR_SOUND;
            $notification->created_at = date('Y-m-d H:i:s');
            $this->getNotificationDao()->save($notification);
            /*Push to APNS*/
            $deviceTokens = $this->getDeviceTokenDao()->fetchAllBy('user_id',$userOwnSound);
            if(count($deviceTokens)) {
                foreach($deviceTokens as $deviceToken) {
                    $this->getSettingsBusiness()->pushNotification(array(
                        'device_token' => $deviceToken->device_token,
                        'alert' => $userLike->display_name.' liked your Program '.$sound->title,
                        'badge' => count($this->getNotificationDao()->fetchAllUnreadNotification($userOwnSound)),
                        'custom' => array(
                            'type' => \Settings\Model\Dto\SettingsDto::PUSH_LIKE_YOUR_SOUND,
                            'id' => $sound->id,
                        ),
                    ));
                }    
            }    
        }
    }
    
    public function pushFollow($data)
    {
        $user_id_audience = $data['id'];
        $user_id_following = $data['user_id'];
        /*Send email and push notification when someone follow*/
        $settings = $this->getSettingsDao()->fetchOneBy('user_id',$user_id_audience);
        $userFollow = $this->getUserDao()->fetchOne($user_id_following);
        /*Send email*/
        if($settings && $settings->email_follow_me == 2) {
            try {
                $this->getSettingsBusiness()->sendEmail(array(
                    'email' => $settings->email,
                    'email_subject' => $userFollow->display_name.' started Turning On your station',
                    'email_body' => $userFollow->display_name.' started Turning On your station',
                ));
            } catch(Exception $e) {}
        }
        /*Push notification*/
        if($settings && $settings->push_follow_me) {
            /*Save to DB*/
            $notification = new \Settings\Model\Dto\NotificationDto;
            $notification->my_user_id = $user_id_audience;
            $notification->user_id = $user_id_following;
            $notification->content = '<b>'.$userFollow->display_name.'</b>'.' started Turning On your station';
            $notification->type = \Settings\Model\Dto\SettingsDto::PUSH_FOLLOW;
            $notification->created_at = date('Y-m-d H:i:s');
            $this->getNotificationDao()->save($notification);
            /*Push to APNS*/
            $deviceTokens = $this->getDeviceTokenDao()->fetchAllBy('user_id',$user_id_audience);
            if(count($deviceTokens)) {
                foreach($deviceTokens as $deviceToken) {
                    $this->getSettingsBusiness()->pushNotification(array(
                        'device_token' => $deviceToken->device_token,
                        'alert' => $userFollow->display_name.' started Turning On your station',
                        'badge' => count($this->getNotificationDao()->fetchAllUnreadNotification($user_id_audience)),
                        'custom' => array(
                            'type' => \Settings\Model\Dto\SettingsDto::PUSH_FOLLOW,
                            'id' => $user_id_following,
                        ),
                    ));
                }    
            }    
        }
    }
    

    public function update($id, $data) {}
    
    public function delete($deviceToken)
    {
        
    }
    
}

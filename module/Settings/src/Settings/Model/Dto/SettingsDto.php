<?php
namespace Settings\Model\Dto;

class SettingsDto
{
    const SOUND_LOW_QUALITY = 1;
    
    const SOUND_MEDIUM_QUALITY = 2;
    
    const SOUND_HIGH_QUALITY = 3;
    
    const PUSH_FOLLOW = 1;
    
    const PUSH_COMMENT_YOUR_POST = 2;
    
    const PUSH_LIKE_YOUR_SOUND = 3;
    
    const PUSH_COMMENT_YOUR_SOUND_YOU_COMMENT = 4;
    
    public function getProperties()
    {
        return array(
            'id',
            'user_id',
            'email',
            'email_follow_me',
            'email_comments_on_my_post',
            'email_comments_on_a_post_i_care',
            'email_like_my_sound',
            'push_follow_me',
            'push_comments_on_my_post',
            'push_comments_on_a_post_i_care',
            'push_like_my_sound',
            'sound_quality',
            'connect_facebook',
            'connect_twitter',
        );
    }
    
    public function exchangeArray($data)
    {        
        foreach($data as $key => $value) {
            if(in_array($key,$this->getProperties())) {
                $this->{$key} = $value;    
            }
        }        
    }
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    
    
}
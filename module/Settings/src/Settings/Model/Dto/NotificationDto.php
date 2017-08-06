<?php
namespace Settings\Model\Dto;

class NotificationDto
{
    const NOTIFICATION_UNREAD = 0;
    
    const NOTIFICATION_READ = 1;
    
    public function getProperties()
    {
        return array('id','my_user_id','user_id','sound_id','content','type','read','created_at');
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
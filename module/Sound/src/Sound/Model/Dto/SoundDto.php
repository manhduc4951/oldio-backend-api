<?php
namespace Sound\Model\Dto;

class SoundDto
{
    const SOUND_TYPE_BROADCAST = 1;
    const SOUND_TYPE_PENDING = 2;
    
    public function getProperties()
    {
        return array('id','user_id','title','thumbnail','thumbnail2','thumbnail3','description','sound_path','duration','type','connect_facebook','connect_twitter','tags','created_at','updated_at');
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
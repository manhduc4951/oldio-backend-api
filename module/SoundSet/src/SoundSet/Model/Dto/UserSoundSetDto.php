<?php
namespace SoundSet\Model\Dto;

class UserSoundSetDto
{
    const SOUND_SET_INACTIVE = 0;
    
    const SOUND_SET_ACTIVE = 1;
    
    public function getProperties()
    {
        return array('id','sound_set_id','user_id','type','order','created_at');
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
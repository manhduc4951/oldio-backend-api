<?php
namespace SoundSet\Model\Dto;

class SoundSetItemDto
{
    
    public function getProperties()
    {
        return array('id','sound_set_id','name','file','created_at');
        //return array('id','sound_set_id','name','image','file','created_at');
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
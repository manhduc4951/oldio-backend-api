<?php
namespace Sound\Model\Dto;

class FavoriteDto
{
    
    public function getProperties()
    {
        return array('id','user_id','sound_id','order','created_at');
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
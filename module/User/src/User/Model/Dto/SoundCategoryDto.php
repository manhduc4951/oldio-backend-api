<?php
namespace User\Model\Dto;

class SoundCategoryDto
{
    
    public function getProperties()
    {
        return array('id','sound_id','category_id');
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